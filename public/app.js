/**
 * Created by tjlytle on 7/14/14.
 */
var translationApp = angular.module('translationApp', []);

translationApp.controller('TranslateCtrl', function($scope, $http){
    $http.get('api.php/lang/source').success(function(data){
        $scope.sources = data;
    });

    $scope.next = function(){
        $('#wizard li.active').next().find('a').tab('show');
        $scope.save();
    }

    $scope.setSource = function(){
        //set the source
        var source = $scope.settings.source;
        //unset the target
        $scope.settings.target = '';

        //update the possible targets
        $http.get('api.php/lang/target/' + source).success(function(data){
            $scope.targets = data;
            //move to the next tab
            $scope.next();
        });

    };

    $scope.save = function(){
        var number = $scope.settings.number;
        var data = {
            source: $scope.settings.source,
            target: $scope.settings.target
        };

        if(!number || !data.source || !data.target){
            return;
        }

        $http({
            method: 'POST',
            url: 'api.php/number/' + number,
            data: $.param(data), //jquery hack
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data){
            $scope.saved = data;
        });
    };
});
