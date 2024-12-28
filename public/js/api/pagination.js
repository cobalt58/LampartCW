class Pagination{
    draw;

    start;
    countTotal;
    countFiltered;

    lengthMenu;
    length;

    ajaxUrl;
    ajaxMethod;

    search;

    paginationContainer;
    itemPerPageContainer;
    paginationButtonsContainer;
    paginationSearchContainer;

    templateFunc;

    emptyText;

    pagId;

    constructor(params) {
        this.pagId = Math.floor(Math.random() * Date.now())
        this.lengthMenu = params.lengthMenu
        this.length = params.pageLength ?? parseInt($(`.pagination-length-${this.pagId}`).val())

        this.draw = -1
        this.start = this.draw*this.start
        this.ajaxUrl = params.ajax.url
        this.ajaxMethod = params.ajax.method

        this.paginationContainer = params.layout.container
        this.itemPerPageContainer = params.layout.lengthMenu
        this.paginationButtonsContainer = params.layout.pagination
        this.paginationSearchContainer = params.layout.search

        this.templateFunc = params.templateFunc
        this.search = params.search
        this.emptyText = params.emptyText


        this.initUi()
        this.initListeners()
        this.changePage(0)
        this.initOnChange()
    }

    changePage(page){
        this.draw = page
        this.start = this.draw*this.length;

        // let nSearch = {}
        // for (const key in this.search) {
        //     if (typeof this.search[key] === "function") {
        //         nSearch[key] = this.search[key]()
        //     }else{
        //         nSearch.key = this.search[key]
        //     }
        // }

        let nSearch = this.getFunctionValues(this.search)
        console.log(this.search)
        console.log(nSearch)
        const data = {
            draw: this.draw,
            length: this.length,
            start: this.start,
            search: {
                ...nSearch,
                value: $(`#search-${this.pagId}`).val(),
            }
        };

        let _this = this;
        $.ajax({
            type: this.ajaxMethod,
            url: this.ajaxUrl,
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (data){
                $(_this.paginationContainer).empty()
                for (const index in data.data) {
                    $(_this.paginationContainer).append($(_this.templateFunc(data.data[index], parseInt(index)+parseInt(_this.start))))
                }
                _this.countTotal = data.recordsTotal
                _this.countFiltered = data.recordsFiltered
                _this.draw = data.draw

                if (_this.draw < 1){
                    $(`.first-page-${_this.pagId}`).addClass('disabled')
                    $(`.prev-page-${_this.pagId}`).addClass('disabled')
                }else{
                    $(`.first-page-${_this.pagId}`).removeClass('disabled')
                    $(`.prev-page-${_this.pagId}`).removeClass('disabled')
                }

                if (_this.draw >= _this.numPages(_this.countTotal)-1){
                    $(`.next-page-${_this.pagId}`).addClass('disabled')
                    $(`.last-page-${_this.pagId}`).addClass('disabled')
                }else{
                    $(`.next-page-${_this.pagId}`).removeClass('disabled')
                    $(`.last-page-${_this.pagId}`).removeClass('disabled')
                }

                $(`.pagination-info-${_this.pagId}`).html(
                    _this.countTotal > 0
                        ? `Показано від ${_this.start+1} по ${(_this.start+_this.length>_this.countFiltered)? _this.countFiltered : _this.start+_this.length} з ${_this.countFiltered} записів`
                        : _this.emptyText ?? `Записів не має`
                )
                $(`.page-btn-${_this.pagId}`).remove()

                const startPage = Math.max(0, _this.draw - 2);
                const endPage = Math.min(_this.draw + 2, _this.numPages(_this.countTotal)-1);

                for (let i = endPage; i > startPage-1; i--) {
                    $(`.prev-page-${_this.pagId}`).after($(`<li class="page-item ${i===_this.draw?'active':''}"><div class="page-link page-btn-${_this.pagId}" data-page="${i}">${i+1}</div></li>`));
                }
            }
        })
    }

    updatePagination(){
        this.changePage(0)
    }

    updatePage(){
        this.changePage(this.draw)
    }

    prevPage(){
        if (this.draw > 0) {
            --this.draw;
            this.changePage(this.draw);
        }
    }

    nextPage(){
        if (this.draw < this.numPages(this.countTotal)) {
            ++this.draw;
            this.changePage(this.draw);
        }
    }

    numPages(count = this.countTotal){
        return Math.ceil(count / this.length);
    }

    initListeners(){
        let _this = this;
        $(document).on('click', `.first-page-${_this.pagId}`,function (event) {
            _this.changePage(0)
        })

        $(document).on('click', `.prev-page-${_this.pagId}`,function (event) {
            _this.prevPage()
        })

        $(document).on('click', `.next-page-${_this.pagId}`,function (event) {
            _this.nextPage()
        })

        $(document).on('click', `.last-page-${_this.pagId}`, function (event) {
            _this.changePage(_this.numPages(_this.countFiltered)-1)
        })

        $(document).on('change', `.pagination-length-${_this.pagId}`,function (event){
            _this.length = parseInt($(this).val())
            _this.changePage(0)
        })

        $(document).on('click', `.page-btn-${_this.pagId}`, function (event) {
            _this.changePage($(this).data('page'))
        })
    }

    initOnChange(){
        let timer;
        let _this = this;
        $(document).on('keyup', `#search-${_this.pagId}`,function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                _this.changePage(0)
            }, 500);
        });

        $(`#search-${_this.pagId}`).on("input", function() {
            if ($(this).val() === "") {
                _this.changePage(0)
            }
        });
    }

    hideElements(){
        $(`${this.paginationSearchContainer}`).hide()
        $(`${this.paginationButtonsContainer}`).hide()
        $(`${this.itemPerPageContainer}`).hide()
    }

    showElements(){
        $(`${this.paginationSearchContainer}`).show()
        $(`${this.paginationButtonsContainer}`).show()
        $(`${this.itemPerPageContainer}`).show()
    }

    initUi() {

        $(`${this.paginationSearchContainer}`).replaceWith($(`
            <div class="d-flex gap-1 my-1 align-items-center">
                <label for="search-${this.pagId}" class="label-pagination-search">Пошук:</label>
                <input type="search" class="form-control form-control-sm" name="search" id="search-${this.pagId}" placeholder="Пошук" aria-controls="users-table">
            </div>
        `))

        $(`${this.paginationButtonsContainer}`).replaceWith($(`
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info-${this.pagId}"></div>
                <nav>
                    <ul class="pagination pagination-sm">
                        <li class="page-item first-page-${this.pagId}"><div class="page-link"><span aria-hidden="true">&laquo;</span></div></li>
                        <li class="page-item prev-page-${this.pagId}"><div class="page-link"><span aria-hidden="true">‹</span></div></li>
                        <li class="page-item next-page-${this.pagId}"><div class="page-link"><span aria-hidden="true">›</span></div></li>
                        <li class="page-item last-page-${this.pagId}"><div class="page-link"><span aria-hidden="true">&raquo;</span></div></li>
                    </ul>
                </nav>
            </div>
        `))

        $(`${this.itemPerPageContainer}`).replaceWith($(`
            <div class="d-flex align-items-center gap-1">
                Показати
                <select class="form-select form-select-sm pagination-length-${this.pagId}" aria-label="Default select example" name="limit">
                    ${this.lengthMenu.map(length => `<option value="${length}">${length}</option>`).join('')}
                </select>
                записів
            </div>
        `))
    }

    getFunctionValues(obj) {
        const result = {};

        for (const key in obj) {
            const value = obj[key];

            if (typeof value === "function") {
                result[key] = value();
            } else if (typeof value === "object" && value !== null) {
                result[key] = this.getFunctionValues(value);
            } else {
                result[key] = value;
            }
        }

        return result;
    }
}

(function($) {
    let methods = {
        init: function (options){
            // Default options
            let defaults = {
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 10,
                ajax: {
                    url: "",
                    method: "GET"
                },
                layout:{
                    search: '.create-pagination-search',
                    lengthMenu: '.create-items-per-page',
                    pagination: '.create-pagination',
                    container: this,
                },
                templateFunc: (data) => "",
                search: {},
                emptyText: "No records found"
            };
            options.layout = $.extend(defaults.layout, options.layout);
            let settings = $.extend(defaults, options);
            console.log(settings)
            let pagination = new Pagination(settings)
            this.each(function() {
                $(this).data("pagination", pagination);
            });

            return pagination
        },
        nextPage: function (){
            $(this).data("pagination").nextPage()
        },
        prevPage: function (){
            $(this).data("pagination").prevPage()
        },
        activatePage: function (page){
            $(this).data("pagination").changePage(page)
        },
        updatePage: function (){
            let pagination = $(this).data("pagination")
            pagination.changePage(pagination.draw)
        },
        update: function (){
            $(this).data("pagination").updatePagination()
        }
    }

    $.fn.pagination = function(options) {
        if (methods[options]) {
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof options === 'object'|| !options) {
            return methods.init.apply(this, [options]);
        } else {
            $.error('Method ' + options + ' does not exists');
        }
    };
})(jQuery);

export {
    Pagination
}