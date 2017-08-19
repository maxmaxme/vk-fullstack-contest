var name, photo

$(function () {


    var hash = getCookie('hash');

    if (hash && checkHash(hash))
        initOrders();
    else
        initAuth();




});

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
                ok = true;
            }
        }
    });

    return ok;
}

function initOrders() {


    $('body').prepend(Mustache.render(mustacheTemplates.menu, {
            userPhoto: photo,
            userName: name
        }) +
        '<div class="container" id="orderPage"></div>');



    $('.navbar ul.js li').click(function () {
        $('.navbar li.active').removeClass('active');
        $(this).addClass('active');
        getOrders($('a', $(this)).attr('href').replace('#', ''));
    });

    var hash = window.location.hash.replace('#', '') || 'new';
    console.log(hash);
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
    }, function () {
         $(button)
             .attr('disabled', true)
             .removeClass('btn-success')
             .addClass('btn-default')
             .html('Завершено');
    });
}

function addNewOrder() {
    $('body').prepend(Mustache.render(mustacheTemplates.addNewOrder));
}

function addNewOrderAction(_this) {
    var $form = $(_this).closest('form'),
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
    });
}

function closePopup(_this) {
    $(_this).closest('.popup_container').remove();
}