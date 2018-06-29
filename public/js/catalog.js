// let catalog = (function ($) {
//
//     let ui = {
//         categoryBtn: $('.js-category'),
//         limit: $('#pages-limit'),
//         pagination: $('#pagination'),
//         $goods: $('#goods'),
//         goodsInfo: $('#goods-info')
//     };
//
//     let goodsTemplate = {
//         big: _.template($('#goods-template-big').html()),
//         // compact: _.template($('#goods-template-compact').html()),
//         // list: _.template($('#goods-template-list').html())
//     };
//     // let pagTemplate = _.template($('#pagination-template').html());
//
//     // Инициализация модуля
//     function init() {
//         _getData({
//             resetPage: true
//         });
//         _bindHandlers();
//     }
//
//     // Привязка событий
//     function _bindHandlers() {
//         ui.categoryBtn.on('click', _changeCategory);
//         ui.limit.on('change', _changeLimit);
//         ui.pagination.on('click', 'a', _changePage);
//     }
//
//     // Смена категории
//     function _changeCategory(e) {
//         let $category = $(e.target);
//         ui.categoryBtn.removeClass('active');
//         $category.addClass('active');
//
//         _getData({
//             resetPage: true
//         });
//     }
//
//     // Смена лимита
//     function _changeLimit() {
//         _getData({
//             resetPage: true
//         });
//     }
//
//     // Смена страницы
//     function _changePage(e) {
//         e.preventDefault();
//         e.stopPropagation();
//
//         let $page = $(e.target).closest('li');
//         ui.pagination.find('li').removeClass('active');
//         $page.addClass('active');
//
//         _getData();
//     }
//
//     // Получение опций-настроек для товаров
//     function _getOptions(resetPage) {
//         let categoryId = +$('.js-category.active').attr('data-category'),
//             page = !resetPage ? +ui.pagination.find('li.active').attr('data-page') : 1,
//             limit = +ui.limit.val();
//
//         return {
//             category: categoryId,
//             page: page,
//             limit: limit
//         }
//     }
//
//     // Получение данных
//     function _getData(options) {
//         let resetPage = options && options.resetPage;
//         options = _getOptions(resetPage);
//
//         $.ajax({
//             url: '/catalog/',
//             data: options,
//             type: 'GET',
//             cache: false,
//             dataType: 'json',
//             success: function (response) {
//                 console.log(response);
//                 // if (response.code === 4) {
//                 console.log(response.entities);
//                 _renderCatalog(response.entities);
//                 // _renderPagination({
//                 //     page: options.page,
//                 //     limit: options.limit,
//                 //     countAll: response.data.countAll,
//                 //     countItems: response.data.goods.length
//                 // });
//                 // } else {
//                 //     console.error('Произошла ошибка', response);
//                 // }
//             }
//         });
//     }
//
//     function _renderCatalog(goods) {
//         let parentTag = $("#goods").html('');
//         // let result = jQuery.parseJSON(goods);
//         $.each(goods, function (id, arr) {
//             parentTag.append('' +
//                 '<div class="small-12 medium-4 large-3 cell">' +
//                 '<img src="/' + arr['imagePath'] + '" alt="book image">' +
//                 '<a href="/catalog/' + id + '"><h5>' + arr['name'] + '</h5></a>' +
//                 '<p>' + arr['description'].substr(0, 70) + '</p>' +
//                 '</div>');
//             // parentTag.append('<div class="grid-container"><div class="grid-x grid-padding-x grid-padding-y">');
//             // $.each(arr, function (id, value) {
//             //     console.log(value);
//             //     parentTag.append('' +
//             //         '<div class="small-12 medium-4 large-3 cell">' +
//             //         '<a href="/catalog/' + id + '"><h4>' + value['name'] + '</h4>></a>' +
//             //         '</div>');
//             // });
//             // console.log('key: ', key);
//             // console.log('arr: ', arr);
//         });
//     }
//
//     //
//     // // Рендер пагинации
//     // function _renderPagination(options) {
//     //     let countAll = options.countAll,
//     //         countItems = options.countItems,
//     //         page = options.page,
//     //         limit = options.limit,
//     //         countPages = Math.ceil(countAll / limit),
//     //         start = (page - 1) * limit + 1,
//     //         end = start + countItems - 1;
//     //
//     //     // Информация о показываемых товарах
//     //     let goodsInfoMsg = start + ' - ' + end + ' из ' + countAll;
//     //     ui.goodsInfo.text(goodsInfoMsg);
//     //
//     //     // Рендер пагинации
//     //     ui.pagination.html(pagTemplate({
//     //         page: page,
//     //         countPages: countPages
//     //     }));
//     // }
//
//     // Экспортируем наружу
//     return {
//         init: init
//     }
//
// })(jQuery);
//
// jQuery(document).ready(catalog.init);