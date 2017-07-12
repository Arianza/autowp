<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Autowp\Comments;

use Application\Model\DbTable\Picture\ModerVote as PictureModerVote;
use Application\Model\DbTable\Picture\Row as PictureRow;
use Application\Model\DbTable\Picture\View as PictureView;

use Zend_Db_Expr;

class Pictures extends AbstractHelper
{
    const
        SCHEME_631 = '631',
        SCHEME_422 = '422';

    /**
     * @var PictureView
     */
    private $pictureViewTable = null;

    /**
     * @var PictureModerVote
     */
    private $moderVoteTable = null;

    /**
     * @var Comments\CommentsService
     */
    private $comments;

    public function __construct(Comments\CommentsService $comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return PictureModerVote
     */
    private function getModerVoteTable()
    {
        return $this->moderVoteTable
            ? $this->moderVoteTable
            : $this->moderVoteTable = new PictureModerVote();
    }

    /**
     * @return PictureView
     */
    private function getPictureViewTable()
    {
        return $this->pictureViewTable
            ? $this->pictureViewTable
            : $this->pictureViewTable = new PictureView();
    }

    private function isPictureModer()
    {
        return $this->view->user()->inheritsRole('pictures-moder');
    }


    public function behaviour(PictureRow $picture)
    {
        return $this->userBehaviour($picture, $this->isPictureModer());
    }

    /**
     * @param array $picture
     * @param bool $isModer
     * @param bool $logedIn
     * @return string
     */
    private function renderBehaviour(array $picture, $isModer)
    {
        return $this->view->partial('application/picture-behaviour', [
            'isModer'        => $isModer,
            'resolution'     => $picture['width'].'×'.$picture['height'],
            'status'         => $picture['status'],
            'cropped'        => $picture['cropped'],
            'cropResolution' => $picture['crop_width'].'×'.$picture['crop_height'],
            'views'          => $picture['views'],
            'msgCount'       => $picture['msgCount'],
            'newMsgCount'    => $picture['newMsgCount'],
            'url'            => $picture['url']
        ]);
    }


    private function userBehaviour(PictureRow $picture, $isModer)
    {
        if ($this->view->user()->logedIn()) {
            $commentsStat = $this->comments->getTopicStatForUser(
                \Application\Comments::PICTURES_TYPE_ID,
                $picture->id,
                $this->view->user()->get()->id
            );
            $msgCount = $commentsStat['messages'];
            $newMsgCount = $commentsStat['newMessages'];
        } else {
            $commentsStat = $this->comments->getTopicStat(
                \Application\Comments::PICTURES_TYPE_ID,
                $picture->id
            );
            $msgCount = $commentsStat['messages'];
            $newMsgCount = 0;
        }

        $data = [
            'url'         => $this->view->pic($picture)->url(),
            'cropped'     => $picture->cropParametersExists(),
            'width'       => $picture['width'],
            'height'      => $picture['height'],
            'crop_width'  => $picture->crop_width,
            'crop_height' => $picture->crop_height,
            'msgCount'    => $msgCount,
            'newMsgCount' => $newMsgCount,
            'views'       => $this->getPictureViewTable()->get($picture),
            'status'      => $picture->status,
        ];

        return $this->renderBehaviour($data, $isModer);
    }


    private function getModerVote(PictureRow $picture)
    {
        $moderVoteTable = $this->getModerVoteTable();
        $db = $moderVoteTable->getAdapter();

        $row = $db->fetchRow(
            $db->select()
                ->from($moderVoteTable->info('name'), [
                    'vote'  => new Zend_Db_Expr('sum(if(vote, 1, -1))'),
                    'count' => 'count(1)'
                ])
                ->where('picture_id = ?', $picture->id)
        );

        if ($row['count'] > 0) {
            return (int)$row['vote'];
        }

        return null;
    }


    public function picture(PictureRow $picture)
    {
        $view = $this->view;

        $isModer = $this->isPictureModer();

        $name = $view->pic()->name($picture, $this->view->language());
        $escName = $view->escape($name);

        $url = $view->pic($picture)->url();

        $imageHtml = $this->view->img($picture->getFormatRequest(), [
            'format'  => 'picture-thumb',
            'alt'     => $name,
            'title'   => $name,
            'shuffle' => true
        ]);

        if ($isModer && $picture->name) {
            $title = $this->view->escapeHtmlAttr($this->view->translate('picture-preview/special-name'));
            $escName = '<span style="color:darkgreen" title="'.$title.'">' .
                              $escName .
                          '</span>';
        }

        $moderVote = $this->getModerVote($picture);

        $classes = ['picture-preview'];
        if ($moderVote !== null) {
            if ($moderVote > 0) {
                $classes[] = 'vote-accept';
            } elseif ($moderVote < 0) {
                $classes[] = 'vote-remove';
            } else {
                $classes[] = 'vote-neutral';
            }
        }

        return '<div class="'.implode(' ', $classes).'">' .
                    '<div class="thumbnail">' .
                        $view->htmlA($url, $imageHtml, false) .
                        '<p>' . $view->htmlA($url, $escName, false) . '</p>' .
                        $this->userBehaviour($picture, $isModer) .
                    '</div>' .
                '</div>';
    }
}
