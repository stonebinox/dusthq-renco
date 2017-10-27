var app=angular.module("renco",[]);
app.controller("pre-book",function($scope,$http,$compile){
    $scope.itemArray=[];
    $scope.totalCost=399;
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
            $(form).attr("name","prebook");
            $(form).attr("method","post");
            $(form).attr("action","book");
            for(var i=0;i<items.length;i++){
                var item=items[i];
                var itemID=item.iditem_master;
                var itemName=stripslashes(item.item_name);
                var itemPrice=item.item_price;
                var formGroup=document.createElement("div");
                $(formGroup).addClass("form-group");
                    var label=document.createElement("label");
                    $(label).attr("for","item"+itemID);
                    $(label).html(itemName+'&nbsp;<span class="badge" id="range'+itemID+'">{{range'+itemID+'}}</span>');
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
            var formGroupEmail=document.createElement("div");
            $(formGroupEmail).addClass("form-group");
                var emailLabel=document.createElement("label");
                $(emailLabel).html("Email");
                $(emailLabel).attr("for","user_email");
                $(formGroupEmail).append(emailLabel);
                var emailField=document.createElement("input");
                $(emailField).attr("type","email");
                $(emailField).attr("name","user_email");
                $(emailField).attr("id","user_email");
                $(emailField).attr("placeholder","Enter a valid email ID");
                $(emailField).addClass("form-control");
                $(emailField).attr("required","true");
                $(formGroupEmail).append(emailField);
            $("#itemlist").append(formGroupEmail);
            var formGroupNumber=document.createElement("div");
            $(formGroupNumber).addClass("form-group");
                var numberLabel=document.createElement("label");
                $(numberLabel).html("Mobile (optional)");
                $(numberLabel).attr("for","user_mobile");
                $(formGroupNumber).append(numberLabel);
                var numberField=document.createElement("input");
                $(numberField).attr("type","tel");
                $(numberField).attr("name","user_mobile");
                $(numberField).attr("id","user_mobile");
                $(numberField).attr("placeholder","Enter a valid mobile number");
                $(numberField).addClass("form-control");
                $(formGroupNumber).append(numberField);
            $("#itemlist").append(formGroupNumber);
            var buttonHolder=document.createElement("div");
            $(buttonHolder).addClass("text-center");
                var button=document.createElement("button");
                $(button).attr("type","button");
                $(button).addClass("btn btn-primary btn-lg");
                $(button).html('Book for <span id="price">{{totalCost}}</span> kr./gang');
                $(buttonHolder).html(button);
            $("#itemlist").append(buttonHolder);
            $compile("#itemlist")($scope);
        }
    };
    $scope.getPrice=function(itemID){
        var itemQuantity=parseInt($("#range"+itemID).text());
        var items=$scope.itemArray.slice();
        var pos=null;
        for(var i=0;i<items.length;i++){
            var item=items[i];
            if(item.iditem_master==itemID){
                pos=i;
                break;
            }
        }
        if(pos!=null){
            var item=items[pos];
            var itemPrice=item.item_price;
            var cost=itemQuantity*itemPrice;
            $scope.totalCost=cost;
        }
    };
});