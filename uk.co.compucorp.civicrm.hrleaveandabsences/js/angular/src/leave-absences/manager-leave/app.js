define([
  'common/angular',
  'common/angularBootstrap',
  'common/text-angular',
  'common/directives/loading',
  'common/services/angular-date/date-format',
  'common/modules/dialog',
  'leave-absences/shared/ui-router',
  'leave-absences/shared/directives/leave-request-popup',
  'leave-absences/shared/models/absence-period-model',
  'leave-absences/shared/models/absence-type-model',
  'leave-absences/manager-leave/modules/config',
  'leave-absences/manager-leave/components/manager-leave-container',
  'leave-absences/manager-leave/components/manager-leave-calendar',
  'leave-absences/manager-leave/components/manager-leave-requests'
], function (angular) {
  angular.module('manager-leave', [
    'ngResource',
    'ngAnimate',
    'ui.router',
    'ui.select',
    'ui.bootstrap',
    'textAngular',
    'common.angularDate',
    'common.models',
    'common.mocks',
    'common.directives',
    'common.dialog',
    'leave-absences.models',
    'manager-leave.config',
    'manager-leave.components',
    'leave-absences.directives',
    'leave-absences.models',
  ])
  .run(['$log', '$rootScope', 'shared-settings', 'settings', function ($log, $rootScope, sharedSettings, settings) {
    $log.debug('app.run');

    $rootScope.pathTpl = sharedSettings.pathTpl;
    $rootScope.settings = settings;
  }]);

  return angular;
});