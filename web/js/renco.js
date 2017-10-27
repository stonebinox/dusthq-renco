var app=angular.module("renco",[]);
app.controller("pre-book",function($scope,$http,$compile){
    $scope.itemArray=[];
    $scope.getItems=function(){
        $http.get("getItems")
        .then(function success(response){
            response=response.data;
            $("#itemlist").css("display","block");
            if((validate(response))&&(response!="INVALID_PARAMETERS"))
            {
                if(typeof response=="object"){
                    $scope.itemArray=response.slice();
                    $scope.displayItems();
                }
                else{
                    response=$.trim(response);
                    switch(response){
                        case "NO_ITEMS_FOUND":
                        messaegBox("No Items","The site is still under development. Please try again later!");
                        break;
                        default:
                        console.log(response);
                        break;
                    }
                }
            }
            else{
                messageBox("Problem","Something went wrong while loading some data. Please try again later. This is the error we see: "+response);
                $("#itemlist").css("display","none");
            }
        },
        function failure(response){
            messageBox("Problem","Something went wrong while loading some data. Please try again later. This is the error we see: "+response);
            $("#itemlist").css("display","none");
        });
    };
    $scope.displayItems=function(){
        if($scope.itemArray.length!=0){
            var items=$scope.itemArray.slice();
            var h3=document.createElement("h3");
            $(h3).html("Make your selection");
            $("#itemlist").html(h3);
            $("#itemlist").css("display","block");
            var form=document.createElement("form");
            for(var i=0;i<items.length;i++){
                var item=items[i];
                var itemID=item.iditem_master;
                var itemName=stripslashes(item.item_name);
                var itemPrice=item.item_price;
                var formGroup=document.createElement("div");
                    var label=document.createElement("label");
                    $(label).attr("for","item"+itemID);
                    $(label).html(itemName+'&nbsp;<span class="badge">{{range'+itemID+'}}</span>');
                    $(formGroup).append(label);
                    var range=document.createElement("input");
                    $(range).attr("type","range");
                    $(range).attr("min","1");
                    $(range).attr("max","10");
                    $(range).val("1");
                    $(range).attr("ng-model","range"+itemID);
                    $(range).attr("ng-init","range"+itemID+"=1");
                    $(range).addClass("form-control");
                    $(range).attr("ng-change","getPrice("+itemID+")");
                    $(formGroup).append(range);
                $("#itemlist").append(formGroup);
            }
            $compile("#itemlist")($scope);
        }
    };
    $scope.getPrice=function(){

    };
});