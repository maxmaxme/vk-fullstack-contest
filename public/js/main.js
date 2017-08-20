var name, photo, balance;

$(function () {


    var hash = getCookie('hash');

    if (hash && checkHash(hash))
        initOrders();
    else
        initAuth();




});

function updateBalance(newBalance) {
    var $balanceBlock = $('#userBalance');

    balance = parseInt(newBalance);

    $('span', $balanceBlock).html(balance);
    $balanceBlock.data('balance', balance);
}

function initAuth() {
    $('body').prepend(Mustache.render(mustacheTemplates.auth))
}

function checkHash(hash) {
    var ok = false;

    $.ajax({
        url: host + 'API/auth.checkHash?hash=' + hash,
        type: 'get',
        dataType: 'json',
        async: false,
        success: function(data) {
            if(data['result']['Name']) {
                name = data['result']['Name'];
                photo = data['result']['Photo'];
                balance = parseInt(data['result']['Balance']);
                ok = true;
            }
        }
    });

    return ok;
}

function initOrders() {


    $('body').prepend(Mustache.render(mustacheTemplates.menu, {
            userPhoto: photo,
            balance: balance,
            userName: name
        }) +
        '<div class="container" id="orderPage"></div>');



    $('.navbar ul.js li').click(function () {
        $('.navbar li.active').removeClass('active');
        $(this).addClass('active');
        getOrders($('a', $(this)).attr('href').replace('#', ''));
    });

    var hash = window.location.hash.replace('#', '') || 'new';
    $('.navbar li#filter-' + hash).click();

}

function getOrders(act) {

    var $ordersContainer = $('#orderPage');

    $ordersContainer.html('');
    //$ordersContainer.html('<div class="loading"></div>');

    api('orders.get', {
        act: act
    }, function (result) {

        if (result.orders.length)
            $ordersContainer.html(Mustache.render(mustacheTemplates.order, {
                orders: result.orders
            }));
        else
            $ordersContainer.html('<div class="text-center">Ничего не найдено');

    });

}

function doOrder(button, orderID) {
    api('orders.do', {
        orderID: orderID
    }, function () {
         $(button)
             .attr('disabled', true)
             .removeClass('btn-success')
             .addClass('btn-default')
             .html('Выполняется');
    });
}

function finishOrder(button, orderID) {
    api('orders.finish', {
        orderID: orderID
    }, function (result) {
         $(button)
             .attr('disabled', true)
             .removeClass('btn-success')
             .addClass('btn-default')
             .html('Завершено');

         updateBalance(result['balance']);
         // todo обновлять баланс в шапке
    });
}

function addNewOrder() {
    $('body').prepend(Mustache.render(mustacheTemplates.addNewOrder));
}
function refillBalance() {
    $('body').prepend(Mustache.render(mustacheTemplates.refillBalance));
}
function withdrawBalance() {
    $('body').prepend(Mustache.render(mustacheTemplates.withdrawBalance));
}

function addNewOrderAction(_this) {
    var $form = $(_this).closest('form'),
        $errorBlock = $('.errorBlock', $form),
        title = $('#title', $form).val(),
        description = $('#description', $form).val(),
        reward = $('#reward', $form).val();


    api('orders.add', {
        title: title,
        description: description,
        reward: reward
    }, function () {
        closePopup(_this);
        $('.navbar li#filter-new').click();
    }, function (error) {
        $errorBlock.html(error);
    });
}

function refillBalanceAction(_this) {
    var $form = $(_this).closest('form'),
        $errorBlock = $('.errorBlock', $form),
        amount = $('#amount', $form).val();


    api('balance.refill', {
        amount: amount
    }, function (result) {
        closePopup(_this);
        updateBalance(result['balance']);
    }, function (error) {
        $errorBlock.html(error);
    });
}

function withdrawBalanceAction(_this) {
    var $form = $(_this).closest('form'),
        $errorBlock = $('.errorBlock', $form),
        amount = $('#amount', $form).val();


    api('balance.withdraw', {
        amount: amount
    }, function (result) {
        closePopup(_this);
        updateBalance(result['balance']);
    }, function (error) {
        $errorBlock.html(error);
    });
}

function closePopup(_this) {
    $(_this).closest('.popup_container').remove();
}