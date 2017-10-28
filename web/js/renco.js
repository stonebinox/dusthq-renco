var app=angular.module("renco",[]);
app.controller("pre-book",function($scope,$http,$compile){
    $scope.itemArray=[];
    $scope.totalCost=399;
    $scope.discount=20;
    $scope.discButton="1week";
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
            $(form).attr("autocomplete","off");
            $(form).attr("ng-init","getPrice()");
            for(var i=0;i<items.length;i++){
                var item=items[i];
                var itemID=item.iditem_master;
                var itemName=stripslashes(item.item_name);
                var itemPrice=item.item_price;
                var formGroup=document.createElement("div");
                $(formGroup).addClass("form-group");
                    var label=document.createElement("label");
                    $(label).attr("for","item"+itemID);
                    $(label).attr("ng-init","range"+itemID+"=1");
                    $(label).html(itemName+'&nbsp;<span class="badge" id="range'+itemID+'">{{range'+itemID+'}}</span>');
                    $(formGroup).append(label);
                    var range=document.createElement("input");
                    $(range).attr("type","range");
                    $(range).attr("min","1");
                    $(range).attr("max","10");
                    $(range).val("1");
                    $(range).attr("id","rangeinput"+itemID);
                    $(range).attr("ng-model","range"+itemID);
                    $(range).addClass("form-control");
                    $(range).attr("ng-change","getPrice()");
                    $(formGroup).append(range);
                    $(formGroup).append('<input type="hidden" name="range[]" value="{{range'+itemID+'}}">');
                $(form).append(formGroup);
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
            $(form).append(formGroupEmail);
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
            $(form).append(formGroupNumber);
            $(form).append('<hr>');
            var buttonParent=document.createElement("div");
            $(buttonParent).addClass("text-center");
            var buttonGroup=document.createElement("div");
            $(buttonGroup).addClass("btn-group");
                var button1=document.createElement("button");
                $(button1).attr("type","button");
                $(button1).attr("ng-click","discount=0;getPrice();discButton='0week';setButtonFocus();");
                $(button1).addClass("btn btn-default");
                $(button1).html("Single payment");
                $(button1).attr("id","0week");
                $(button1).attr("title","No discount");
                $(button1).attr("data-toggle","tooltip");
                $(button1).attr("data-placement","auto");
            $(buttonGroup).html(button1);
                var button2=document.createElement("button");
                $(button2).attr("type","button");
                $(button2).attr("ng-click","discount=20;getPrice();discButton='1week';setButtonFocus();");
                $(button2).addClass("btn btn-primary");
                $(button2).html("Every week");
                $(button2).attr("id","1week");
                $(button2).attr("title","20% discount");
                $(button2).attr("data-toggle","tooltip");
                $(button2).attr("data-placement","auto");
            $(buttonGroup).append(button2);
                var button3=document.createElement("button");
                $(button3).attr("type","button");
                $(button3).attr("ng-click","discount=10;getPrice();discButton='2week';setButtonFocus();");
                $(button3).addClass("btn btn-default");
                $(button3).html("Every two weeks");
                $(button3).attr("id","2week");
                $(button3).attr("title","10% discount");
                $(button3).attr("data-toggle","tooltip");
                $(button3).attr("data-placement","auto");
            $(buttonGroup).append(button3);
                var button4=document.createElement("button");
                $(button4).attr("type","button");
                $(button4).attr("ng-click","discount=0;getPrice();discButton='4week';setButtonFocus();");
                $(button4).addClass("btn btn-default");
                $(button4).html("Every four weeks");
                $(button4).attr("id","4week");
                $(button4).attr("title","No discount");
                $(button4).attr("data-toggle","tooltip");
                $(button4).attr("data-placement","auto");
            $(buttonGroup).append(button4);
            $(buttonParent).html(buttonGroup);
            $(form).append(buttonParent);
            $(form).append('<hr>');
            var buttonHolder=document.createElement("div");
            $(buttonHolder).addClass("text-center");
                var button=document.createElement("button");
                $(button).attr("type","submit");
                $(button).addClass("btn btn-primary btn-lg");
                $(button).html('Book for <span id="price">{{totalCost}}</span> kr./gang');
                $(buttonHolder).html(button);
            $(form).append('<input type="hidden" name="disc" value="{{discButton}}">');
            $(form).append(buttonHolder);
            $("#itemlist").append(form);
            $compile("#itemlist")($scope);
            $('[data-toggle="tooltip"]').tooltip({
                trigger: "hover"
            });
        }
    };
    $scope.getPrice=function(){
        var items=$scope.itemArray.slice();
        var cost=399;
        for(var i=0;i<items.length;i++){
            var item=items[i];
            var itemID=item.iditem_master;
            var itemPrice=item.item_price;
            var itemQuantity=parseInt($("#rangeinput"+itemID).val());
            if(itemQuantity>1){
                var price=(itemQuantity-1)*itemPrice;
                cost+=price;
            }
        }
        var discount=$scope.discount;
        cost=cost-((discount/100)*cost);
        $scope.totalCost=cost;
    };
    $scope.setButtonFocus=function(){
        var id=$scope.discButton;
        $(".btn-group").find("button").removeClass("btn-primary");
        $(".btn-group").find("button").addClass("btn-default");
        $("#"+id).removeClass("btn-default");
        $("#"+id).addClass("btn-primary");
    };
});
app.controller("booking",function($scope,$http,$compile){
    $scope.booking_id;

});
app.controller("booking",function($scope,$http,$compile){
    $scope.totalCost=399;
    $scope.getBookingDetails=function(){
        $http.get("getBookingDetails")
        .then(function success(response){
            response=response.data;
            if(typeof response == "object"){

            }
            else{
                response=$.trim(response);
                switch(response){
                    
                }
            }
        },
        function failure(response){

        });
    };
});