
CTFrontendBuilder.controller("ControllerConditions", function($scope, $parentScope, $timeout) {    

    /**
     * Get all the conditoins applied to element. Used in Copy/Paste
     *
     * @since 4.10
     * @author Ilya K.
     */

    $scope.getGlobalConditions = function() {

        var id = $scope.component.active.id;

        return $scope.component.options[id]['model']['globalconditions'] || false;
    }


    /**
     * Set the conditoins to element. Used in Copy/Paste
     *
     * @since 4.10
     * @author Ilya K.
     */

    $scope.setGlobalConditions = function(conditions) {

        // base 64 decode
        conditions = atob(conditions);

        // make sure JSON object is not broekn
        try {
            var conditionsObj = JSON.parse(conditions);
        } catch (error) {
            return false;
        }

        // check all fields are present
        if (!conditionsObj.conditions) return false;
        if (!conditionsObj.hash) return false;

        // check hash
        var hash = $scope.stringToHash(JSON.stringify(conditionsObj.conditions));
        if (conditionsObj.hash !== hash) return false;

        // all clear to update
        $scope.setOptionModel('globalconditions', conditionsObj.conditions); 
        $scope.setOptionModel('conditionstype', conditionsObj.conditionstype); 
        $scope.setOptionModel('conditionspreview', conditionsObj.inEditorBehavior); 
        $parentScope.evalGlobalConditions();

        return true;
    }


    /**
     * Copy current conditions to clipboard
     *
     * @since 4.10
     * @author Ilya K.
     */

    $scope.copyConditions = function() {

        var returnObj = {
            conditions: $scope.getGlobalConditions(),
            inEditorBehavior: $scope.getOption('conditionspreview'),
            conditionstype: $scope.getOption('conditionstype'),
            hash: $scope.stringToHash(JSON.stringify($scope.getGlobalConditions())),
        };

        // convert to JSON and base64 encode
        var code = JSON.stringify(returnObj);
        code = btoa(code);

        $scope.copyToClipboard(code);
        $scope.showNoticeModal("<div>Element Conditions copied to clipboard!</div>", "ct-notice");
    }


    /**
     * Apply passed conditions
     *
     * @since 4.10
     * @author Ilya K.
     */

    $scope.pasteConditions = function(conditions) {
    
        if(!conditions) {
            return;
        }

        var result = $scope.setGlobalConditions(conditions)
        if (result) {
            $scope.showNoticeModal("<div>Element Conditions applied from your code!</div>", "ct-notice");
        }
        else {
            $scope.showNoticeModal("<div>Element Conditions NOT applied. Code parsing issue.</div>");
            console.log(conditions);
        }
    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */
    
    $scope.getGlobalCondition = function(index) {

        var id = $scope.component.active.id;

        if (undefined!=$scope.component.options[id]['model']['globalconditions'] &&
            undefined!=$scope.component.options[id]['model']['globalconditions'][index] && 
            undefined!=$scope.component.options[id]['model']['globalconditions'][index]['name']) {
    
            return $scope.globalConditions[$scope.component.options[id]['model']['globalconditions'][index]['name']];
        }

        return false;
    }

    
    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionValue = function(index, condition) {

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return '';
        }
        
        if (globalCondition['values']['keys'] && 
            globalCondition['values']['options'][condition.value]) {

            return globalCondition['values']['options'][condition.value];
        }
        else {
            return condition.value;
        }
    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionPlaceholder = function(index) {

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return 'Custom Value...';
        }

        return globalCondition['values']['placeholder'] ?
               globalCondition['values']['placeholder'] : 'Custom Value...';
    }


    /**
     * Custom condition means user can type in any custom value
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isCustomCondition = function(index) {
        
        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return false;
        }
        
        return globalCondition['values']['custom'];
    }


    /**
     * AJAX conditions loads list of options with AJAX
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isAJAXCondition = function(index) {
        
        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition || undefined==globalCondition['values']) {
            return false;
        }
        
        return globalCondition['values']['ajax'];
    }


    /**
     * Moved logic from conditions.modal.view.php
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.updateConditionOptions = function(index, data, searchValue) {

        if (undefined==index||undefined==data) {
            return;
        }

        if (""!==searchValue) {
            var valueFound = false;
            for (var key in data) {
                if (data.hasOwnProperty(key) && data[key] == searchValue) {
                    $scope.setConditionValue(index, key, searchValue);
                    valueFound = true;
                    break;
                }
            }
            if (!valueFound) {
                // set negative index to unset the value, as there is no real life negative ID terms
                $scope.setConditionValue(index, -1, searchValue);
            }
        }

        var globalCondition = $scope.getGlobalCondition(index);

        if (undefined==globalCondition) {
            console.log('No global condition found with index: ' + index);
            return;
        }

        globalCondition['values']['options'] = data;
    }


    /**
     * Check the active component options to find the name for the condition with passed index 
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.getConditionName = function(index) {

        var id = $scope.component.active.id;

        if ($scope.component.options[id]['model']['globalconditions'] &&
            $scope.component.options[id]['model']['globalconditions'][index] &&
            $scope.component.options[id]['model']['globalconditions'][index]['name'] ) {
        
            return $scope.component.options[id]['model']['globalconditions'][index]['name'];
        }

        return "";
    }


    /**
     * Check if condition suppose to store keys instead of names/values
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.isKeysCondition = function(index) {

        var conditionName = $scope.getConditionName(index);
        
        if ( $scope.globalConditions[conditionName] &&
             $scope.globalConditions[conditionName]['values'] &&
             $scope.globalConditions[conditionName]['values']['keys']) {
            
            return true;
        } 

        return false;
    }


    /**
     * Sometimes we need to store thing like ID keys, check if so and set the proper value
     *
     * @since 3.3
     * @author Ilya K.
     */

    $scope.setConditionValue = function(index, key, value) {

        var id = $scope.component.active.id;
        
        if ( $scope.isKeysCondition(index) && undefined !== key) {
            $scope.component.options[id]['model']['globalconditions'][index]['value'] = key;
        }
        else {
            $scope.component.options[id]['model']['globalconditions'][index]['value'] = value;
        }

        // value to display in search field, it should always be the name and not the key
        $scope.component.options[id]['model']['globalconditions'][index]['searchValue'] = value;

        // update tree
        $scope.setOptionModel('globalconditions', $scope.component.options[id]['model']['globalconditions']);
    }

});