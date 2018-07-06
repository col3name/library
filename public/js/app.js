'use strict';

$(document).foundation();
// $('[data-open="exampleModal11"]').on('click', function() {
//     $('#exampleModal11').foundation('toggle');
// });

class DropDown {
    constructor(mapped) {
        this.mapped = mapped;
        this.dropdownContainer = $('[data-dropdown-container="' + mapped + '"]');
        this.dropdownBtn = $('[data-dropdown-button="' + mapped + '"]');
        this.dropdownList = $('[data-dropdown-list="' + mapped + '"]');
        this.quantity = $('[data-dropdown-quantity="' + mapped + '"]');
        this.action = $('[data-dropdown-action="' + mapped + '"]');
    }

    _handle() {
        let self = this;

        this.dropdownContainer
            .on('click', '[data-dropdown-button="' + self.mapped + '"]', function (e) {
                if (self.dropdownList.hasClass('hidden')) {
                    self.action.removeClass('fi-plus');
                    self.action.addClass('fi-minus');
                    self.dropdownList.removeClass('hidden');
                    setTimeout(function () {
                        self.dropdownList.removeClass('visuallyHidden');
                    }, 100);
                } else {
                    self.action.removeClass('fi-minus');
                    self.action.addClass('fi-plus');
                    self.dropdownList.addClass('visuallyHidden');
                    self.dropdownList.one('transitionend', function (e) {
                        self.dropdownList.addClass('hidden');
                    });
                }
            })
            .on('input', '[data-dropdown-search="' + self.mapped + '"]', self._inputHandle)
            .on('change', '[type="checkbox"]', this._changeHandler);
    }

    _inputHandle() {
        let target = $(this);
        let search = target.val().toLowerCase();

        let $li = $('li.choices-item');
        if (!search) {
            $li.show();
            return false;
        }

        $li.each(function () {
            let text = $(this).text().toLowerCase();
            let match = text.indexOf(search) > -1;
            $(this).toggle(match);
        });
    }

    _changeHandler() {
        let numChecked = $('[type="checkbox"]:checked').length;
        let quantity = $('.quantity');
        quantity.text(numChecked || 'Любой');
    }
}

let addTagButton = $('<button type="button" class="button add_tag_link">Add a tag</button>');
let newLinkLi = $('<li></li>').append(addTagButton);

jQuery(document).ready(function () {
    let collectionHolder = $('ul.tags');
    collectionHolder.append(newLinkLi);
    collectionHolder.data('index', collectionHolder.find(':input').length);

    collectionHolder.find('li').each(function () {
        addTagFormDeleteLink($(this));
    });

    addTagButton.on('click', function (e) {
        addTagForm(collectionHolder, newLinkLi);
    });
});

function addTagFormDeleteLink(tagFormLi) {
    let $removeFormButton = $('<button type="button" class="button">Delete this tag</button>');
    tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', function (e) {
        tagFormLi.remove();
    });
}

function addTagForm(collectionHolder, newLinkLi) {
    let prototype = collectionHolder.data('prototype');
    console.log('prototype', prototype);
    let index = collectionHolder.data('index');
    if (index < 5) {
        let newForm = prototype;
        newForm = newForm.replace(/__name__/g, index);

        collectionHolder.data('index', index + 1);

        let $newFormLi = $('<li></li>').append(newForm);
        newLinkLi.before($newFormLi);
    }
}

class Scroll {
    constructor(from, to, timeOut) {
        this.from = $(from);
        this.to = $(to);
        this.timeOut = timeOut;
    }

    scroll() {
        let self = this;
        this.from.click(function () {
            this.prevOffset = self.to.offset();
            $('html, body').animate({
                scrollTop: self.to.offset().top
            }, self.timeOut);
        })
    }
}

class Search {
    constructor(search, entitySelector, url, template) {
        this.search = $(search);
        this.entitySelector = $(entitySelector);
        console.log('this.entitySelector', this.entitySelector);
        this.url = url;
        this.template = template;
    }

    handle() {
        let self = this;
        let path = window.location.pathname;

        if (path !== '/search') {
            let showSearchResult = function () {
                self.entitySelector.addClass('search-active');
                self.entitySelector.removeClass('hide');
            };
            let hideSearchResult = function () {
                self.entitySelector.removeClass('search-active');
                self.entitySelector.addClass('hide')
            };

            self.search.focus(showSearchResult);
            self.entitySelector.hover(showSearchResult);
            self.search.focusout(hideSearchResult);
        }

        self.search.keyup(function () {
            self.entitySelector.empty();
            const MIN_LENGTH = 3;
            let value = self.search.val();

            if (value.length >= MIN_LENGTH) {

                $.ajax({
                    url: self.url,
                    data: {q: value},
                    type: 'GET',
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                        self._renderTemplate(response)
                    }
                });
            }
        });
    }

    _renderTemplate(response) {
        let self = this;
        self.entitySelector.empty();
        $.each(response, function (key, arr) {
            self.template(arr, self.entitySelector);
        });
    }
}

const template = {
    books: {
        large: function (arr, entitySelector) {
            function trimDescription(description, length) {
                description = description.length > length ? description.substr(0, length) : description;
                return description;
            }

            let description = trimDescription(arr['description'], 150);

            entitySelector.append('<div class="media-object">' +
                '  <div class="media-object-section">' +
                '    <div class="thumbnail">' +
                '      <img class="small-image" class="small-image" src= "' + arr['imagePath'] + '">' +
                '    </div>' +
                '  </div>' +
                '  <div class="media-object-section main-section">' +
                '    <h4><a href="/catalog/' + arr['bookCopyId'] + '">' + arr['name'] + '</a></h4>\n' +
                '    <p> ' + description + '</p>\n' +
                '  </div>\n' +
                '</div><li></li>');
        },
        small: function (arr, entitySelector) {
            console.log(entitySelector);
            entitySelector.append('<li><p><a href="/catalog/' + arr['bookCopyId'] + '">' + arr['name'] + '</a></p></li>');
        }
    },

    tag: function (arr, entitySelector) {
        for (let i = 0; i < arr.length; i++) {
            entitySelector.append('<li><p>' + arr[i]['name'] + '</p></li>');
        }
    }
};

class Bookmark {
    constructor(button, countLike, action) {
        this.like = $(button);
        this.countLike = $(countLike);
        this.action = action;
    }

    handle() {
        let self = this;
        this.like.on('click', function () {
            let bookCopyId = self.like.attr('data-bookCopyId');
            let url = '/catalog/' + bookCopyId + self.action;
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
                let countLike = $('#countLike');
                countLike.text(response['countLike']);
            }
        })
    }
}

let filter = (function ($) {
    let categoryBtn = $('.js-category');
    let limit = $('#pageLimit');
    let pagination = $('#pagination');
    let goods = $('#goods');
    let goodsInfo = $('#goods-info');
    let genreChoise = $('#genreChoise');
    let authorChoise = $('#authorChoise');
    let sort = $('#sort');
    let reset = $('#resetFilter');

    let ui = {
        categoryBtn: categoryBtn,
        limit: limit,
        pagination: pagination,
        goods: goods,
        goodsInfo: goodsInfo,
        genreChoise: genreChoise,
        authorChoise: authorChoise,
        sort: sort,
        reset: reset,
    };

    let template = {
        showLarge: function (data) {
            return '<div class="small-6 medium-4 cell">' +
                '<div class="post-module">' +
                '    <div class="thumbnail">' +
                '        <img  src="/' + data.imagePath + '" alt="book image">' +
                '    </div>' +
                '    <div class="post-content">' +
                '        <h3><a href="/catalog/' + data.id + '">' + data.name + '</a></h3>' +
                '        <p class="description">' + data.description + '</p>\n' +
                '    </div>' +
                '    </div>' +
                '</div>';
        }
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
        ui.pagination.on('click', 'a', _changePage);
        ui.reset.on('click', _changeReset);
    }

    function _changeCategory(e) {
        let $category = $(e.target);
        ui.categoryBtn.removeClass('pagination-active');
        $category.addClass('pagination-active');

        _changeGetData();
    }

    function _changeGetData() {
        console.log('change');
        _getData({
            resetPage: true
        });
    }

    function _changeReset() {
        $('#sort').val("withoutSort");
        $('#pageLimit').val("8");
        $('#genreChoise').val("all");
        $('#authorChoise').val("all");

        _changeGetData();
    }

    function _changePage(e) {
        e.preventDefault();
        e.stopPropagation();

        let $page = $(e.target).closest('li');
        ui.pagination.find('li').removeClass('pagination-active');
        $page.addClass('pagination-active');

        _getData();
    }

    function _getSelectedOrderBy() {
        let selected = $('#sort option:selected');
        return selected.attr('data-orderBy');
    }

    function _getOptions(resetPage) {
        let page = !resetPage ? ui.pagination.find('li.pagination-active').attr('data-page') : 1;
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
        console.log(options);
        $.ajax({
            url: '/catalog/',
            data: options,
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.entities.error === 'not found') {
                    ui.goods.html('');
                    ui.pagination.html('');
                    ui.goods.append('<p>не найдено</p>')
                } else {
                    let optionsRenderPagiation = {
                        page: options.page,
                        limit: options.limit,
                        countAll: (response.countAll == null) ? 0 : response.countAll['0']['1'],
                        countItems: response.countItem
                    };

                    _renderCatalog(response.entities, showType);
                    _renderPagination(optionsRenderPagiation);
                }
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
        let parentTag = ui.goods.html('');
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

        ui.pagination.html(pagTemplate({
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
        showMessage: $('#showMessage')
    };

    function init() {
        console.log('returnBook', ui.returnBook);
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
            console.log('good', response);
            ui.takenBook.remove();
            ui.showMessage().text()
        };

        let error = function (response) {
            console.log('err', response);
            ui.takenBook.remove();
            ui.showMessage.text('Успешно');
            ui.showMessage.removeClass('hide');
        };

        _getData(url, success, error);
    }

    function _returnClickHandle() {
        let issuanceId = ui.returnBook.attr('data-issuanceId');
        console.log(issuanceId);
        let url = '/profile/return-book/' + issuanceId;

        ui.showMessage.html('hello');

        let success = function (response) {
            console.log('good', response['error']);
            let error = response['error'];
            if (!error) {
                ui.returnBook.remove();
            }

            let msg = error ? 'Не удалось вернуть' : 'успешно';
            ui.showMessage.removeClass('hide');
            ui.showMessage.html(msg);
        };
        let error = function (response) {
            console.log('err', response);
            ui.showMessage.html('Не удалось вернуть книгу');
            ui.showMessage.removeClass('hide');
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
            dataType: 'json',
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
        takenBooks.append('<div class="small-6 medium-4 large-3 cell">' +
            '<img class="small-image" src="/' + value['imagePath'] + '" alt="">' +
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

class Hamburger {
    constructor(menu, menuBox) {
        this.menu = $(menu);
        this.menuBox = $(menuBox);
    }

    handle() {
        let self = this;
        this.menu.on('click', function () {
            self.menu.toggleClass('active');
            self.menuBox.toggleClass('show');
            self.menuBox.toggleClass('active');
        });
    }
}

class Alert {
    constructor($tag) {
        this.tag = $($tag);
    }

    _delete() {
        let self = this;
        self.tag.on('click', function () {
            let parent = $(this).parent();
            parent.remove();
        });
    }
}

class FileUploadPreview {
    constructor(mapped) {
        this.imageInput =  $('[data-upload-image="' + mapped + '"]');
        console.log('this.imageInput', this.imageInput);
        this.imageInput.append('<h1>imageInput</h1>')
        this.showPreview = $('[data-show-preview="' + mapped + '"]');
        console.log(this.showPreview);
        this.showPreview.append('<h1>showPreview</h1>')
    }

    _showPreview() {
        this.imageInput.change(function(){
            readURL(this);
        });
    }

    _readURL(input) {
        let self = this;
        if (input.files && input.files[0]) {
            console.log('readUrl');

            let reader = new FileReader();

            reader.onload = function (e) {
                input.showPreview.attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
}

function readURLS(input) {
    if (input.files && input.files[0]) {

        let reader = new FileReader();

        reader.onload = function(e) {
            $('.image-upload-wrap').hide();

            $('.file-upload-image').attr('src', e.target.result);
            $('.file-upload-content').show();

            $('.image-title').html(input.files[0].name);
        };

        reader.readAsDataURL(input.files[0]);

    } else {
        removeUpload();
    }
}

function removeUpload() {
    $('.file-upload-input').replaceWith($('.file-upload-input').clone());
    $('.file-upload-content').hide();
    $('.image-upload-wrap').show();
}
$('.image-upload-wrap').bind('dragover', function () {
    $('.image-upload-wrap').addClass('image-dropping');
});
$('.image-upload-wrap').bind('dragleave', function () {
    $('.image-upload-wrap').removeClass('image-dropping');
});


function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('[data-show-preview="avatar"]').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$('[data-upload-image="avatar"]').change(function(){
    readURL(this);
});

class DeleteAvatar {
    constructor(deleteBtn) {
        this.deleteBtn = $(deleteBtn);
    }

    _handle() {
        let self = this;
        this.deleteBtn.on('click', function() {
            let id = self.deleteBtn.attr('data-user')

            console.log('id', id);

            $.ajax({
                url: '/profile/' + id + '/delete-avatar',
                data: {id: id},
                type: 'POST',
                cache: false,
                dataType: 'json',
                success: function (response) {
                    console.log('response');
                    // self._renderTemplate(response)
                }
            });
        });
    }
}

class Application {

}

let app = (function ($) {
    function init() {

        let deleteAvatar = new DeleteAvatar('#deleteAvatar');
        deleteAvatar._handle();

        let avatarPreview = new FileUploadPreview('avatar');
        avatarPreview._showPreview();

        filter.init();
        issuance.init();
        let scroll = new Scroll('#click', '#scrollTo', 100);
        scroll.scroll();

        let showMore = new ShowMore('#showMore', '#showBooks');
        showMore.handle();

        let rating = new Rating('#bookRating');
        rating.activate();

        let like = new Bookmark('#like', '#countLike', '/favorite-book');
        like.handle();

        let readedBook = new Bookmark('#readedBook', '#countLike', '/change-readed-book');
        readedBook.handle();

        let search = new Search('#search', '#entitiesNav', '/search', template.books.large);
        search.handle();

        let headerSearch = new Search('#headerSearch', '#entitiesNav', '/search', template.books.small);
        headerSearch.handle();

        let tagSearch = new Search('#searchTag', '#searchTagResult', '/search-tag', template.tag);
        tagSearch.handle();

        let hamburger = new Hamburger('.menu2', '.menu-box');
        hamburger.handle();

        let tagDropDown = new DropDown("tag");
        tagDropDown._handle();

        let authorDropDown = new DropDown("authors");
        authorDropDown._handle();

        let authorsBookDropDown = new DropDown("authorsBook");
        authorsBookDropDown._handle();

        let genresBookDropDown = new DropDown("genresBook");
        genresBookDropDown._handle();

        let message = new Alert('button[data-dismiss="alert"]');
        message._delete();
    }

    function deleteAlert($tag) {
        $($tag).click(function () {
            let parent = $(this).parent();
            parent.remove();
        });
    }

    return {
        init: init
    }
})(jQuery);

jQuery(document).ready(app.init);