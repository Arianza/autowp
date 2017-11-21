import angular from 'angular';
import Module from 'app.module';
import template from './template.html';
import notify from 'notify';
import { chunkBy } from 'chunk';

const CONTROLLER_NAME = 'CutawayController';
const STATE_NAME = 'cutaway';

angular.module(Module)
    .config(['$stateProvider',
        function config($stateProvider) {
            $stateProvider.state( {
                name: STATE_NAME,
                url: '/cutaway/:page',
                controller: CONTROLLER_NAME,
                controllerAs: 'ctrl',
                template: template,
                params: {
                    page: {
                        replace: true,
                        value: '',
                        reload: true,
                        squash: true
                    }
                }
            });
        }
    ])
    .controller(CONTROLLER_NAME, [
        '$scope', '$http', '$state',
        function($scope, $http, $state) {
            
            var ctrl = this;
            
            ctrl.chunks = [];
            ctrl.paginator = null;
            
            $scope.pageEnv({
                layout: {
                    blankPage: false,
                    needRight: false
                },
                name: 'page/109/name',
                pageId: 109
            });
                
            $http({
                method: 'GET',
                url: '/api/picture',
                params: {
                    status: 'accepted',
                    fields: 'owner,thumbnail,votes,views,comments_count,name_html,name_text',
                    limit: 18,
                    page: $state.params.page,
                    perspective_id: 9,
                    order: 15
                }
            }).then(function(response) {
                ctrl.chunks = chunkBy(response.data.pictures, 6);
                ctrl.paginator = response.data.paginator;
            }, function(response) {
                notify.response(response);
            });
        }
    ]);

export default CONTROLLER_NAME;
