<?php

namespace Application\Model;

use Laminas\Db\TableGateway\TableGateway;

class PictureVote
{
    private TableGateway $voteTable;

    private TableGateway $summaryTable;

    public function __construct(TableGateway $voteTable, TableGateway $summaryTable)
    {
        $this->voteTable    = $voteTable;
        $this->summaryTable = $summaryTable;
    }

    public function vote(int $pictureId, int $userId, int $value): void
    {
        $value = $value > 0 ? 1 : -1;

        $sql = '
            insert into picture_vote (picture_id, user_id, value, timestamp)
            values (?, ?, ?, now())
            on duplicate key update
                value = VALUES(value),
                timestamp = VALUES(timestamp)
        ';

        /* @phan-suppress-next-line PhanUndeclaredMethod */
        $statement = $this->voteTable->getAdapter()->query($sql);
        $statement->execute([(int) $pictureId, (int) $userId, $value]);

        $this->updatePictureSummary($pictureId);
    }

    public function getVote(int $pictureId, int $userId): array
    {
        $row = null;
        if ($userId) {
            $row = $this->voteTable->select([
                'picture_id' => $pictureId,
                'user_id'    => $userId,
            ])->current();
        }

        $summary = $this->summaryTable->select([
            'picture_id' => $pictureId,
        ])->current();

        return [
            'value'    => $row ? $row['value'] : null,
            'positive' => $summary ? $summary['positive'] : 0,
            'negative' => $summary ? $summary['negative'] : 0,
        ];
    }

    private function updatePictureSummary(int $pictureId): void
    {
        $sql = '
            insert into picture_vote_summary (picture_id, positive, negative)
            values (
                ?,
                (select count(1) from picture_vote where picture_id = ? and value > 0),
                (select count(1) from picture_vote where picture_id = ? and value < 0)
            )
            on duplicate key update
                positive = VALUES(positive),
                negative = VALUES(negative)
        ';
        /* @phan-suppress-next-line PhanUndeclaredMethod */
        $statement = $this->summaryTable->getAdapter()->query($sql);
        $statement->execute([$pictureId, $pictureId, $pictureId]);
    }
}
