'use strict';

class Search {
    constructor(search, entitySelector) {
        this.search = $(search);
        this.entitySelector = $(entitySelector).html('');
    }

    handle() {
        let self = this;
        let path = window.location.pathname;
        console.log(path);
        if (path !== '/search') {
            self.search.focus(function () {
                self.entitySelector.addClass('search-active');
                self.entitySelector.removeClass('hide');
            });
            self.search.focusout(function () {
                self.entitySelector.removeClass('search-active');
                self.entitySelector.addClass('hide')
            });
        }
        self.search.keyup(function () {
            let minlength = 3;
            let that = this;
            let value = $(this).val();
            if (value.length >= minlength) {
                $.ajax({
                    url: '/search',
                    data: {
                        'q': value
                    },
                    type: 'GET',
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                        let entitySelector = $("#entitiesNav").html('');
                        let path = window.location.pathname;
                        if (path === '/search') {
                            self._renderBooksLarge(response, self.entitySelector);
                        } else {
                            self._renderBooksSmall(response, self.entitySelector);
                        }
                    }
                });
            }
        });
    }

    _renderBooksLarge(response, entitySelector) {
        $.each(response, function (key, arr) {
            entitySelector.append('<div class="media-object">' +
                '  <div class="media-object-section">' +
                '    <div class="thumbnail">' +
                '      <img class="small-image" src= "' + arr['imagePath'] + '">' +
                '    </div>' +
                '  </div>' +
                '  <div class="media-object-section main-section">' +
                '    <h4><a href="/catalog/' + arr['bookCopyId'] + '">' + arr['name'] + '</a></h4>\n' +
                '    <p> ' + arr['description'] + '</p>\n' +
                '  </div>\n' +
                '</div><li></li>');
        });
    }

    _renderBooksSmall(response, entitySelector) {
        $.each(response, function (key, arr) {
            let position = entitySelector.position();
            console.log(position);
            entitySelector.append('<li><p><a href="/catalog/' + arr['bookCopyId'] + '">' + arr['name'] + '</a></p></li>');
        });
    }
}

class Like {
    constructor(like) {
        this.like = $(like);
        console.log(this.like);
    }

    handle() {
        let self = this;

        this.like.on('click', function () {
            let bookCopyId = self.like.attr('data-bookCopyId');
            let url = '/catalog/' + bookCopyId + '/favorite-book';
            self._getData(url);
        });
    }

    _getData(url) {
        $.ajax({
            url: url,
            data: {},
            type: 'GET',
            cache: false,
            dataType: 'json',
            error: function (response) {
                console.log('err', response);
            },
            success: function (response) {
                console.log(response);
            }
        })
    }
}

let catalogPag = (function ($) {

    let categoryBtn = $('.js-category');
    let $limit = $('#pages-limit');
    let pagination = $('#pagination');
    let goods = $('#goods');
    let goodsInfo = $('#goods-info');
    let genreChoise = $('#genreChoise');
    let authorChoise = $('#authorChoise');
    let sort = $('#sort');
    let reset = $('#resetFilter');

    let ui = {
        categoryBtn: categoryBtn,
        limit: $limit,
        pag: pagination,
        goods: goods,
        goodsInfo: goodsInfo,
        genreChoise: genreChoise,
        authorChoise: authorChoise,
        sort: sort,
        reset: reset,
    };

    let template = {
        showMedium: function (data) {
            return '<div class="small-12 cell">' +
                '<div class="media-object-section">' +
                '<div class="thumbnail">' +
                '<img src="/' + data.imagePath + '" alt="book image">' +
                '</div>' +
                '</div>' +
                '<div class="media-object-section main-section">' +
                '<a href="/catalog/' + data.id + '"><h5>' + data.name + '</h5></a>' +
                '<p>\' + data.description + \'</p>' +
                '</div>' +
                '</div>'
                ;
        },
        showLarge: function (data) {
            return '<div class="small-12 medium-4 cell">' +
                '<img src="/' + data.imagePath + '" alt="book image">' +
                '<a href="/catalog/' + data.id + '"><h5>' + data.name + '</h5></a>' +
                '<p>' + data.description + '</p>' +
                '</div>';
        },
    };

    function init() {
        _getData({
            resetPage: true
        });
        _bindHandlers();
    }

    function _bindHandlers() {
        ui.categoryBtn.on('click', _changeCategory);
        ui.limit.on('change', _changeGetData);
        ui.genreChoise.on('change', _changeGetData);
        ui.authorChoise.on('change', _changeGetData);
        ui.sort.on('click', _changeGetData);
        ui.pag.on('click', 'a', _changePage);
        ui.reset.on('click', _changeReset);
    }

    function _changeCategory(e) {
        let $category = $(e.target);
        ui.categoryBtn.removeClass('active');
        $category.addClass('active');

        _changeGetData();
    }

    function _changeGetData() {
        _getData({
            resetPage: true
        });
    }

    function _changeReset() {
        $('#sort').val("withoutSort");
        $('#pages-limit').val("4");
        $('#genreChoise').val("all");
        $('#authorChoise').val("all");

        _changeGetData();
    }

    function _changePage(e) {
        e.preventDefault();
        e.stopPropagation();

        let $page = $(e.target).closest('li');
        ui.pag.find('li').removeClass('active');
        $page.addClass('active');

        _getData();
    }

    function _getSelectedOrderBy() {
        let selected = $('#sort option:selected');
        return selected.attr('data-orderBy');
    }

    function _getOptions(resetPage) {
        let page = !resetPage ? ui.pag.find('li.active').attr('data-page') : 1;
        let limit = ui.limit.val();
        let genreId = ui.genreChoise.val();
        let authorId = ui.authorChoise.val();
        let sortField = ui.sort.val();
        let orderBy = _getSelectedOrderBy();

        return {
            page: page,
            limit: limit,
            genreId: genreId,
            authorId: authorId,
            sortField: sortField,
            orderBy: orderBy
        };
    }

    function _getData(options) {
        let resetPage = options && options.resetPage;
        let showType = options && options.showType;

        options = _getOptions(resetPage);
        $.ajax({
            url: '/catalog/',
            data: options,
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);

                _renderCatalog(response.entities, showType);
                _renderPagination({
                    page: options.page,
                    limit: options.limit,
                    countAll: (response.countAll == null) ? 0 : response.countAll['0']['1'],
                    countItems: response.countItem
                });
            }
        });
    }

    function parseDescription(arr) {
        if (arr['description'] == null) {
            return '';
        }

        let description = arr['description'];
        return description.length > 70 ? description.substr(0, 70) : description;
    }

    function _renderCatalog(goods, showType) {
        let parentTag = $("#goods").html('');

        $.each(goods, function (id, arr) {
            let data = {
                id: id,
                name: arr['name'],
                imagePath: arr['imagePath'],
                description: parseDescription(arr),
            };

            parentTag.append(template.showLarge(data));
        });
    }

    let pagTemplate = _.template($('#pagination-template').html());

    function _renderPagination(options) {
        let countAll = options.countAll;
        let countItems = options.countItems;
        let page = options.page;
        let limit = options.limit;
        let countPages = Math.ceil(countAll / limit);
        let start = (page - 1) * limit + 1;
        let end = (page * limit > countAll) ? countAll : (page * limit);
        let nextPage = page;
        nextPage++;

        let goodsInfoMsg = start + ' - ' + end + ' из ' + countAll;
        ui.goodsInfo.text(goodsInfoMsg);

        ui.pag.html(pagTemplate({
            page: page,
            countPages: countPages,
            nextPage: nextPage
        }));
    }

    return {
        init: init
    }
})(jQuery);

let issuance = (function ($) {
    let ui = {
        takenBook: $("#takenBook"),
        returnBook: $("#returnBook"),
    };

    function init() {
        console.log('returnBook', ui.returnBook);
        // _getData();
        _bindHandlers()
    }

    function _getData(url, success, error) {
        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: success,
            error: error
        });
    }

    function _bindHandlers() {
        ui.takenBook.on('click', _takenClickHandle);
        ui.returnBook.on('click', _returnClickHandle);
    }

    function _takenClickHandle() {
        let bookCopyId = ui.takenBook.attr('data-bookCopyId');
        let url = '/catalog/' + bookCopyId + '/take-book';

        console.log(bookCopyId);
        let success = function (response) {
            console.log('good');
            ui.takenBook.remove();
        };

        let error = function (response) {
            console.log('err', response);
        };

        _getData(url, success, error);
    }

    function _returnClickHandle() {
        let issuanceId = ui.returnBook.attr('data-issuanceId');
        console.log(issuanceId);
        let url = '/profile/return-book/' + issuanceId;

        let success = function (response) {
            console.log('good');
            ui.returnBook.remove();

        };
        let error = function (response) {
            console.log('err', response);
        };

        _getData(url, success, error);
    }

    return {
        init: init
    }

})(jQuery);

class ShowMore {
    constructor(showMore, takenBooks) {
        this.showMore = $(showMore);
        this.takenBooks = $(takenBooks);
    }

    handle() {
        let self = this;
        let page = self.showMore.attr('data-page');

        this.showMore.click({'page': page}, function () {
            page++;
            self.showMore.attr('data-page', page);
            self.clickHandle(self.showMore, self.takenBooks, page);
        });
    }

    clickHandle(showMore, takenBooks, page) {
        let url = '/history-issuance';
        let data = {
            'page': page
        };
        let self = this;
        console.log(takenBooks);
        $.ajax({
            url: url,
            data: data,
            type: 'GET',
            cache: false,
            success: function (response) {
                self.renderResponse(response, takenBooks, showMore);
            },
            error: function (response) {
                console.log("error: " + response)
            }
        });
    }

    renderResponse(response, takenBooks, showMore) {
        console.log(response);
        let self = this;
        $.each(response, function (key, arr) {
            self.renderBooks(arr, key, takenBooks, showMore);
        });
    }

    renderBooks(arr, key, takenBooks, showMore) {
        let self = this;

        $.each(arr, function (id, value) {
            if (key === 'entities') {
                if (id !== 'error') {
                    ShowMore.renderBook(value, takenBooks);
                } else {
                    showMore.addClass('not-fount-animate');
                    $('#error').html('Больше неn заказов');
                }
            }
        });
    }

    static renderBook(value, takenBooks) {
        let description = value['description'];
        description = (description.length > 70) ? description.substr(0, 70) + '...' : description;
        takenBooks.append('<div class="small-12 medium-6 large-4 cell">' +
            '<img src="/' + value['imagePath'] + '" alt="">' +
            '<a href="/catalog/' + value['id'] + '">' + value['name'] + '</a>' +
            '<p>' + description + '</p></div>');
    }

}

class Rating {
    constructor(bookRating) {
        this.bookRating = $(bookRating);
    }

    activate() {

        let options = {
            starOff: '/image/star-off.png',
            starOn: '/image/star-on.png',
            click: Rating._clickHandle()
        };

        this.bookRating.raty(options)
    }

    static _clickHandle(score) {
        return function (score) {
            let self = this;
            let pathname = $(location).attr('pathname');
            let url = pathname + '/rate-book';

            $.ajax({
                url: url,
                data: {
                    score: score
                },
                type: 'GET',
                cache: false,
                success: function (response) {
                    $('#bookRating').parent().empty();
                    $('#bookRating').unwrap();
                },
                error: function (response) {
                    $('#bookRating').append('<p>Что-то пошло не так:)</p>')
                }
            });
        };
    }
}

let app = (function ($) {
    function init() {
        //
        // $(document).foundation();
        // $('#element').foundation('scrollToLoc', '#el');
        catalogPag.init();
        issuance.init();
        let showMore = new ShowMore('#showMore', '#showBooks');
        let rating = new Rating('#bookRating');
        let like = new Like('#like');
        let search = new Search('#search', '#entitiesNav');

        showMore.handle();
        rating.activate();
        like.handle();
        search.handle();
    }

    return {
        init: init
    }
})(jQuery);

let $body = $('body');
jQuery(document).ready(app.init);


