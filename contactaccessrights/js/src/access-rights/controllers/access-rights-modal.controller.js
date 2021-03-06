/* eslint-env amd */

define([
  'common/lodash'
], function (_) {
  'use strict';

  AccessRightsModalController.$inject = ['$q', '$uibModalInstance', 'Region', 'Location', 'Right'];

  function AccessRightsModalController ($q, $modalInstance, Region, Location, Right) {
    var vm = this;

    vm.dataLoaded = false;
    vm.errorMsg = '';
    vm.submitting = false;
    vm.availableData = {
      regions: [],
      locations: []
    };
    vm.originalData = {
      locations: [],
      regions: []
    };
    vm.selectedData = {
      locations: [],
      regions: []
    };

    vm.cancel = cancel;
    vm.submit = submit;

    (function init () {
      $q.all([
        Region.getAll(),
        Location.getAll()
      ])
        .then(function (values) {
          return {
            regions: values[0],
            locations: values[1]
          };
        })
        .then(function (values) {
          return $q.all(_.map(values, function (value, key) {
            vm.availableData[key] = value;
            return Right['get' + _.capitalize(key)]();
          }));
        })
        .then(function (values) {
          return {
            regions: values[0],
            locations: values[1]
          };
        })
        .then(function (values) {
          Object.keys(values).forEach(function (key) {
            vm.originalData[key] = values[key].values;
            vm.selectedData[key] = values[key].values.map(function (entity) {
              return entity.entity_id;
            });
          });
        })
        .then(function () {
          vm.dataLoaded = true;
        });
    }());

    /**
     * Closes the modal
     */
    function cancel () {
      $modalInstance.dismiss('cancel');
    }

    /**
     * Saves the new values, and deletes the removed ones
     *
     * @param  {string} type  Either "regions" or "locations"
     * @return {Promise}      The result of all promises
     */
    function persistValues (type) {
      var originalData = vm.originalData[type];
      var selectedData = vm.selectedData[type];

      var originalEntityIds = originalData.map(function (i) {
        return i.entity_id;
      });
      var newEntityIds = _.difference(selectedData, originalEntityIds);
      var removedRightIds = _.difference(originalEntityIds, selectedData)
        .map(function (entityId) {
          return _.find(originalData, function (i) {
            return i.entity_id === entityId;
          }).id;
        });

      var promises = [];

      if (newEntityIds.length > 0) {
        promises.push(Right['save' + _.capitalize(type)](newEntityIds));
      }
      if (removedRightIds.length > 0) {
        promises.push(Right.deleteByIds(removedRightIds));
      }

      return $q.all(promises);
    }

    /**
     * Saves data and closes the modal
     */
    function submit () {
      vm.submitting = true;

      $q.all([persistValues('regions'), persistValues('locations')])
        .then(function () {
          $modalInstance.dismiss('cancel');
        })
        .catch(function () {
          vm.errorMsg = 'Error while saving data';
        })
        .finally(function () {
          vm.submitting = true;
        });
    }
  }

  return { AccessRightsModalController: AccessRightsModalController };
});
