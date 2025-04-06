document.addEventListener('livewire:navigated', () => {

    $(".loading-state").addClass("d-none");
    let store_slug = $("#store_slug").val();
    let current_store_id = $("#current_store_id").val();
    if (store_slug == null || store_slug == "" || store_slug == undefined) {
        store_slug = $("#default_store_slug").val();
    }
    let custom_url = $("#custom_url").val();
    let current_url = $("#current_url").val();

    let primaryColor = $("#store-primary-color").val();
    let secondaryColor = $("#store-secondary-color").val();
    let linkActiveColor = $("#store-link-active-color").val();
    let linkHoverColor = $("#store-link-hover-color").val();
    const root = document.documentElement;
    // Set the CSS variables
    root.style.setProperty("--primary-color", primaryColor);
    root.style.setProperty("--secondary-color", secondaryColor);
    root.style.setProperty("--link-active-color", linkActiveColor);
    root.style.setProperty("--link-hover-color", linkHoverColor);

    $(".slider-link").on("click", function (e) {
        let link = $(this).data("link");
        Livewire.navigate(link);
    });

    var currency_symbol = $("#currency").val();

    $(".changeLang").on("click", function () {
        let lang = $(this).data("lang-code");
        Livewire.dispatch("changeLang", { lang });
    });
    $(".changeCurrency").on("click", function () {
        let currency = $(this).data("currency-code");
        Livewire.dispatch("changeCurrency", { currency });
    });
    if ($("#user_id").val() >= 1) {
        is_logged = true;
    } else {
        is_logged = false;
    }

    $(document).on("change", "#prTearm", function () {
        if (!$(this).prop("checked")) {
            $(".cart-checkout").addClass("disabled");
        } else {
            $(".cart-checkout").removeClass("disabled");
        }
    });

    function proceedToCheckoutHandler() {
        if (is_logged == false) {
            iziToast.error({
                message: "Please Login First",
                position: "topRight",
            });
            setTimeout(() => {
                Livewire.navigate(appUrl + "/login");
            }, 1500);
            return false;
        }
        Livewire.navigate(appUrl + "cart/checkout");
        return false;
    }

    $(document)
        .off("click", ".proceed-to-checkout")
        .on("click", ".proceed-to-checkout", proceedToCheckoutHandler);

    function cartBtnHandler() {
        if (is_logged == false) {
            iziToast.error({
                message: "Please Login First",
                position: "topRight",
            });
            setTimeout(() => {
                Livewire.navigate(appUrl + "/login");
            }, 1500);
            return false;
        }
        Livewire.navigate(appUrl + "cart/");
        return false;
    }

    $(document)
        .off("click", ".cart-btn")
        .on("click", ".cart-btn", cartBtnHandler);
    Shareon.init();

    function product_image_swap() {
        $(".swatchLbl").on("click", function () {
            $(this).parent().siblings(".active").removeClass("active");
            $(this).parent().addClass("active");
        });
    }
    product_image_swap();

    if (is_logged == false) {
        //display local cart
        let cart = localStorage.getItem("cart");
        cart = localStorage.getItem("cart") != null ? JSON.parse(cart) : [];
        display_cart(cart);

        // remove from local storage cart
        $(document).on("click", ".remove_from_cart", function () {
            let product_variant_id = $(this).data("variant-id");
            let cart = localStorage.getItem("cart");
            cart = localStorage.getItem("cart") != null ? JSON.parse(cart) : [];
            let item_to_delete = cart.findIndex(
                (item) => item.product_variant_id == product_variant_id
            );
            if (item_to_delete !== -1) {
                cart.splice(item_to_delete, 1);
            }
            localStorage.setItem("cart", JSON.stringify(cart));
            let cart_count = cart.length;
            $(".cart-count").text(cart_count);
            $(".cart_count").text(cart_count);
            display_cart(cart);
        });
    }
    //remove from cart
    window.addEventListener("remove_from_cart", (e) => {
        let variant_id = e.detail.data.variant_id;
        let store_id = e.detail.data.store_id;
        let user_id = e.detail.data.user_id;
        let product_type = e.detail.data.product_type;
        let cart_count = e.detail.data.cart_count;
        $.ajax({
            type: "POST",
            url: appUrl + "cart/remove-from-cart",
            data: {
                user_id: user_id,
                product_variant_id: variant_id,
                product_type: product_type,
                store_id: store_id,
            },
            success: function (response) {
                if (response.error == false) {
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    if (response.data.length >= 1) {
                        cart_count = response.data.cart_items.length;
                    } else {
                        cart_count = "0";
                    }
                    $(".cart-count").text(cart_count);
                    let data = {
                        variant_id: variant_id,
                        product_type: product_type,
                    };
                    Livewire.dispatch("refreshComponent");
                    return;
                }
                iziToast.error({
                    message: response.message,
                    position: "topRight",
                });
            },
        });
    });
    // add to favorite
    $(".add-favorite").on("click", function () {
        let product_id = $(this).data("product-id");
        let product_type = $(this).data("product-type");
        if (product_type == undefined || product_type == null) {
            product_type = "regular";
        }
        let user_id = $("#user_id").val();
        let t = $(this);
        if (user_id == null || user_id == "") {
            iziToast.error({
                message: "Please Login First",
                position: "topRight",
            });
            return;
        }
        $.ajax({
            url: appUrl + "product/add-to-favorite",
            method: "POST",
            data: {
                product_id,
                user_id,
                product_type,
            },
            dataType: "json",
            success: function (response) {
                if (response.error == false) {
                    if (t.hasClass("card_fav_btn")) {
                        t.addClass("remove-favorite")
                            .removeClass("add-favorite")
                            .children("ion-icon")
                            .attr("name", "heart")
                            .addClass("text-danger");
                    } else {
                        $(".add-favorite")
                            .removeClass("d-flex")
                            .addClass("d-none");
                        $(".remove-favorite")
                            .removeClass("d-none")
                            .addClass("d-flex");
                    }
                    $(".wishlist-count").text(response["wishlist_count"]);
                    iziToast.success({
                        message:
                            "An item has been successfully added to your wishlist.",
                        position: "topRight",
                    });
                }
            },
        });
    });

    window.addEventListener("quickview", (event) => {
        setTimeout(() => {
            if ($(".kv-ltr-theme-svg-star").length > 0) {
                $(".kv-ltr-theme-svg-star").rating({
                    hoverOnClear: false,
                    theme: "krajee-svg",
                });
            }
            attributes();
            add_cart();
            Shareon.init();
        }, 1000);
    });

    // remove from favorite
    $(document).on("click", ".remove-favorite", function (e) {
        e.preventDefault();

        let productId = $(this).attr("data-product-id");
        let productType = $(this).attr("data-product-type");
        if (productType == undefined || productType == null) {
            productType = "regular";
        }
        let user_id = $("#user_id").val();
        let t = $(this);
        Swal.fire({
            title: "Do you Really want to Remove This from Wishlist?",
            showCancelButton: true,
            confirmButtonText: "Yes Remove",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: appUrl + "product/remove-from-favorite",
                    method: "POST",
                    data: {
                        productId,
                        user_id,
                        productType,
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.error == false) {
                            if (t.hasClass("card_fav_btn")) {
                                t.addClass("add-favorite")
                                    .removeClass("remove-favorite")
                                    .children("ion-icon")
                                    .attr("name", "heart-outline")
                                    .removeClass("text-danger");
                            } else {
                                $(".rem-fav-btn")
                                    .removeClass("d-flex")
                                    .addClass("d-none");
                                $(".add-favorite")
                                    .removeClass("d-none")
                                    .addClass("d-flex");
                            }
                            $(".wishlist-count").text(
                                response["wishlist_count"]
                            );
                            Livewire.dispatch("refreshComponent");
                            iziToast.success({
                                message:
                                    "The item has been removed from your wishlist.",
                                position: "topRight",
                            });
                        }
                    },
                });
            }
        });
    });

    // add to compare
    $(".add-compare").on("click", function (e) {
        let product_id = $(this).attr("data-product-id");
        let product_type = $(this).attr("data-product-type");
        if (product_type == undefined || product_type == null) {
            product_type = "regular";
        }
        e.preventDefault();
        let compare_item = {
            product_id: product_id.trim(),
            product_type: product_type.trim(),
        };
        let compare = localStorage.getItem("compare");

        compare = compare !== null ? JSON.parse(compare) : null;
        if (compare !== null && compare !== undefined) {
            if (compare.find((item) => item.product_id === product_id)) {
                iziToast.error({
                    message:
                        "This Product is already present in your Compare List",
                    position: "topRight",
                });
                return;
            }
            compare.push(compare_item);
        } else {
            compare = [compare_item];
        }
        localStorage.setItem("compare", JSON.stringify(compare));
        iziToast.success({
            message: "Product Added To Compare",
            position: "topRight",
        });
    });

    $(".star-rating").on("rating:change", function (event, value, caption) {
        Livewire.dispatch("updateRating", { update_rating: value });
    });

    function attributes() {
        $(".attributes").on("change", function (e) {
            e.preventDefault();
            let prices = [];
            let variant_ids = [];
            let variant_prices = [];
            let variant = [];
            let variants = [];
            let attributes_length = [];
            let selected_attributes = [];
            let is_variant_available = false;
            let price = "";
            let spacial_price = "";
            $(".variants").each(function () {
                prices = {
                    price: $(this).data("price"),
                    spacial_price: $(this).data("special_price"),
                    dealer_price: $(this).data("dealer_price"),
                };
                variant_ids.push($(this).data("id"));
                variant_prices.push(prices);
                variant = $(this).val().split(",");
                variants.push(variant);
            });
            attributes_length = variant.length;
            $(this).parent().siblings().children().prop("checked", false);
            $(".attributes").each(function (i, e) {
                if ($(this).prop("checked")) {
                    selected_attributes.push($(this).val());
                    var selected_variant_id = "";
                    if (selected_attributes.length == attributes_length) {
                        prices = [];
                        var selected_variant_id = "";
                        $.each(variants, function (i, e) {
                            if (arrays_equal(selected_attributes, e)) {
                                is_variant_available = true;
                                prices.push(variant_prices[i]);
                                selected_variant_id = variant_ids[i];
                            }
                        });
                        if (is_variant_available) {
                            $("#add_cart").attr(
                                "data-product-variant-id",
                                selected_variant_id
                            );
                            $(".modal_add_cart").attr(
                                "data-product-variant-id",
                                selected_variant_id
                            );
                            $(".dlt-add-cart").attr(
                                "data-product-variant-id",
                                selected_variant_id
                            );
                            if (
                                prices[0].spacial_price < prices[0].price &&
                                prices[0].spacial_price != 0
                            ) {
                                price = parseFloat(prices[0].spacial_price);
                                spacial_price = parseFloat(prices[0].price);
                                $(".add_cart").attr(
                                    "data-variant-price",
                                    prices[0].spacial_price
                                );
                                $(".product_price").html(
                                    currency_symbol + price.toFixed(2)
                                );
                                $("#special_price").html(currency_symbol + spacial_price.toFixed(2));
                            } else {
                                price = parseFloat(prices[0].price);
                                $(".add_cart").attr(
                                    "data-variant-price",
                                    prices[0].price
                                );
                                $(".product_price").html(
                                    currency_symbol + price.toFixed(2)
                                );
                                $("#special_price").html("");
                            }
                            //show dealer price and difference between price and dealer price
                            const dealer_price = parseFloat(prices[0].dealer_price);
                            const dealer_price_box = document.getElementById("dealer_price");
                            const diff_price_box = document.getElementById("diff_price");
                            if (dealer_price_box) {
                                $("#dealer_price").html(
                                    currency_symbol + dealer_price.toFixed(2)
                                )
                            }
                            if (diff_price_box) {
                                $("#diff_price").html(
                                    currency_symbol + (price - dealer_price).toFixed(2)
                                )
                            }
                            $(".add_cart").removeAttr("disabled");
                        } else {
                            price = "No variant Available!";
                            $(".product_price").html(price);
                            $(".add_cart").attr("disabled", "true");
                        }
                    }
                }
            });
        });
    }
    attributes();

    initialize();
    if ($(".kv-ltr-theme-svg-star").length > 0) {
        $(".kv-ltr-theme-svg-star").rating({
            hoverOnClear: false,
            theme: "krajee-svg",
        });
    }

    function goBack() {
        parent.history.back();
    }
    $(document).on("click", ".remove_compare_item", function (e) {
        e.preventDefault();
        let product_id = $(this).attr("data-product-id");

        var compare_count = $("#compare_count").text();
        compare_count--;

        $("#compare_count").html(compare_count);
        $(".compare" + product_id).remove();
        if (compare_count == 0) {
            $(".compare_item").remove();
            let comp = "";
            comp +=
                '<div id="page-content"><div class="container"><div class="row">' +
                '<div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">' +
                '<p><img src="' +
                appUrl +
                "frontend/elegant/images/empty-img.gif" +
                '" alt="image"  width="500" /></p>' +
                '<h2 class="fs-4 mt-4"><strong>SORRY,</strong> This Compare is currently empty</h2>' +
                '<p class="same-width-btn"><a wire:navigate href="#" onClick="goBack()" class="btn btn-secondary btn-lg mb-2 mx-3">GO Back</a></p>' +
                "</div></div></div> </div>`";
            $("#compare_item").html(comp);
        }

        let compare = localStorage.getItem("compare");
        compare = compare !== null ? JSON.parse(compare) : null;
        if (compare) {
            let new_compare = compare.filter(function (item) {
                return item.product_id != product_id;
            });
            localStorage.setItem("compare", JSON.stringify(new_compare));
            display_compare();
        }
    });
    let compareUrl = custom_url.split("?");
    if (compareUrl[0] === appUrl + "compare") {
        if (localStorage.getItem("compare")) {
            var compare = localStorage.getItem("compare").length;
            compare = compare !== null ? JSON.parse(compare) : null;
            if (compare) {
                display_compare();
            }
        }
    }

    function cart_sync() {
        let cart = localStorage.getItem("cart");
        cart = localStorage.getItem("cart") != null ? JSON.parse(cart) : "";
        if (cart != "" && cart != undefined) {
            let product_variant_id = [];
            let qty = [];
            let product_type = [];
            let store_id = [];
            cart.forEach((e) => {
                product_variant_id.push(e.product_variant_id);
                qty.push(e.qty);
                product_type.push(e.product_type);
                store_id.push(e.store_id);
            });
            $.ajax({
                type: "POST",
                url: appUrl + "cart/cart-sync",
                data: {
                    product_variant_id: product_variant_id,
                    qty: qty,
                    product_type: product_type,
                    store_id: store_id,
                    is_saved_for_later: false,
                },
                dataType: "json",
                success: function (response) {
                    if (response.error == false) {
                        Livewire.dispatch("refreshComponent");
                        $(".cart-count").text(response.cart_count);
                        localStorage.removeItem("cart");
                        iziToast.success({
                            message: response.message,
                            position: "topRight",
                        });
                        return;
                    }
                    localStorage.removeItem("cart");
                    iziToast.error({
                        message: response.message,
                        position: "topRight",
                    });
                    return;
                },
            });
            return;
        }
        return;
    }
    if (is_logged == true) {
        cart_sync();
    }

    initListner("click", ".select-store", function () {
        let store_id = $(this).data("store-id");
        let store_name = $(this).data("store-name");
        let store_image = $(this).data("store-image");
        let store_slug = $(this).data("store-slug");

        $.ajax({
            type: "POST",
            url: "/set_store",
            data: {
                store_id,
                store_name,
                store_image,
                store_slug,
            },
            success: function (data) {
                if (data) {
                } else {
                    iziToast.error({
                        message: "Error In Setting Store",
                        position: "topRight",
                    });
                }
                Livewire.navigate(current_url);
            },
        });
    });

    $(document).on("click", ".store-show", function () {
        $(".stores-main").addClass("sticky-stores-active");
        $(".store-show")
            .children("ion-icon")
            .attr("name", "chevron-forward-outline");
        $(this).removeClass("store-show");
        $(this).addClass("store-hide");
    });

    $(document).on("click", ".store-hide", function () {
        $(".stores-main").removeClass("sticky-stores-active");
        $(".store-hide")
            .children("ion-icon")
            .attr("name", "chevron-back-outline");
        $(this).removeClass("store-hide");
        $(this).addClass("store-show");
    });

    function setUrlParameter(url, paramName, paramValue) {
        paramName = paramName.replace(/\s+/g, "-");
        if (paramValue == null || paramValue == "") {
            return url
                .replace(
                    new RegExp("[?&]" + paramName + "=[^&#]*(#.*)?$"),
                    "$1"
                )
                .replace(new RegExp("([?&])" + paramName + "=[^&]*&"), "$1");
        }
        var pattern = new RegExp("\\b(" + paramName + "=).*?(&|#|$)");
        if (url.search(pattern) >= 0) {
            return url.replace(pattern, "$1" + paramValue + "$2");
        }
        url = url.replace(/[?#]$/, "");
        return (
            url +
            (url.indexOf("?") > 0 ? "&" : "?") +
            paramName +
            "=" +
            paramValue
        );
    }

    $(document).on("change", "#SortBy", function (e) {
        e.preventDefault();
        var sort = $(this).val();
        let link = setUrlParameter(location.href, "sort", sort);
        Livewire.navigate(link);
    });
    $(document).on("click", ".list_view", function (e) {
        e.preventDefault();
        var list_view = $(this).data("value");
        let link = setUrlParameter(location.href, "mode", list_view);
        Livewire.navigate(link);
    });

    $(document).on("change", "#perPage", function (e) {
        e.preventDefault();
        var perPage = $(this).val();
        let link = setUrlParameter(location.href, "perPage", perPage);
        Livewire.navigate(link);
    });

    $(document).on("click", ".bySearch", function (e) {
        e.preventDefault();
        let search = $(".search_text").val();
        let search_text = search.split(" ").join("_");
        let link = setUrlParameter(appUrl + "products/", "search", search_text);
        if (search != "") {
            Livewire.navigate(link);
        }
    });
    $(document).on("click", ".quick-view-modal", function (e) {
        e.preventDefault();
        let product_id = $(this).data("product-id");
        let product_type = $(this).data("product-type");
        if (product_type == undefined || product_type == null) {
            product_type = "regular";
        }
        if (product_id != null && product_id != undefined) {
            Livewire.dispatch("quick_view", {
                id: product_id,
                product_type: product_type,
            });
        }
    });

    $("#search-drawer").on("shown.bs.offcanvas", function () {
        $(".searchInput").trigger("focus");
    });

    $("#quickview_modal").on("hidden.bs.modal", function () {
        Livewire.dispatch("clear_quickview_modal");
    });

    function getUrlParameter(sParam, custom_url = "") {
        sParam = sParam.replace(/\s+/g, "-");
        if (custom_url != "") {
            if (custom_url.indexOf("?") > -1) {
                var sPageURL = custom_url.substring(
                    custom_url.indexOf("?") + 1
                );
            } else {
                return undefined;
            }
        } else {
            var sPageURL = window.location.search.substring(1);
        }

        var sURLVariables = sPageURL.split("&"),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split("=");

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined
                    ? true
                    : decodeURIComponent(sParameterName[1]);
            }
        }
    }

    function buildUrlParameterValue(
        paramName,
        paramValue,
        action,
        custom_url = ""
    ) {
        if (custom_url != "") {
            var param = getUrlParameter(paramName, custom_url);
        } else {
            var param = getUrlParameter(paramName);
        }
        if (action == "add") {
            if (param == undefined) {
                param = paramValue;
            } else {
                param += "|" + paramValue;
            }
            return param;
        } else if (action == "remove") {
            if (param != undefined) {
                param = param.split("|");
                param.splice($.inArray(paramValue, param), 1);
                return param.join("|");
            } else {
                return "";
            }
        }
    }

    $(document).on("change", ".product-filter", function (e) {
        e.preventDefault();
        var attribute_name = $(this).data("attribute");
        attribute_name = "filter-" + attribute_name;
        var get_param = getUrlParameter(attribute_name);
        var current_param_value = $(this).val();
        if (get_param == undefined) {
            get_param = "";
        }
        if (this.checked) {
            var param = buildUrlParameterValue(
                attribute_name,
                current_param_value,
                "add",
                custom_url
            );
        } else {
            var param = buildUrlParameterValue(
                attribute_name,
                current_param_value,
                "remove",
                custom_url
            );
        }
        custom_url = setUrlParameter(custom_url, attribute_name, param);
    });

    $(".product-filter-btn").on("click", function (e) {
        e.preventDefault();
        Livewire.navigate(custom_url);
    });

    $(document).on("click", ".logout", function (e) {
        e.preventDefault();
        $.ajax({
            type: "get",
            url: appUrl + "login/logout",
            data: "",
            dataType: "json",
            success: function (response) {
                iziToast.success({
                    message: response.message,
                    position: "topRight",
                });
                //location.reload();
                location.href = "/";
                return;
            },
        });
    });
    $(document).on("change", ".brand", function (e) {
        e.preventDefault();
        let t = $(this).val();
        let u = $(this)
            .parent()
            .siblings()
            .children(".brand")
            .prop("checked", false)
            .val();
        var param = buildUrlParameterValue("brand", u, "remove", custom_url);
        custom_url = setUrlParameter(custom_url, "brand", param);
        if (this.checked) {
            var param = buildUrlParameterValue("brand", t, "add", custom_url);
        } else {
            var param = buildUrlParameterValue(
                "brand",
                t,
                "remove",
                custom_url
            );
        }
        custom_url = setUrlParameter(custom_url, "brand", param);
    });

    // price slider
    function price_slider() {
        let min_price = $("#min-price").val();
        let max_price = $("#max-price").val();
        let selected_min_price = $("#selected_min_price").val();
        let selected_max_price = $("#selected_max_price").val();
        $("#slider-range").slider({
            range: true,
            min: parseInt(min_price),
            max: parseInt(max_price),
            values: [selected_min_price, selected_max_price],
            slide: function (event, ui) {
                $("#amount").val(
                    currency_symbol +
                    ui.values[0] +
                    " - " +
                    currency_symbol +
                    ui.values[1]
                );
                $("#min-price").val(ui.values[0]);
                $("#max-price").val(ui.values[1]);
            },
        });
        $("#amount").val(
            currency_symbol +
            $("#slider-range").slider("values", 0) +
            " - " +
            currency_symbol +
            $("#slider-range").slider("values", 1)
        );
    }
    price_slider();

    initListner("click", ".price-filter-btn", function (e) {
        e.preventDefault();
        let min_price = $("#min-price").val();
        let max_price = $("#max-price").val();

        custom_url = setUrlParameter(custom_url, "min_price", min_price);
        custom_url = setUrlParameter(custom_url, "max_price", max_price);
        Livewire.navigate(custom_url);
    });

    $(".show_tabs").on("click", function () {
        $(".widget-title").trigger("click");
        $(this).addClass("d-none");
        $(".close_tabs").removeClass("d-none");
    });
    $(".close_tabs").on("click", function () {
        $(".widget-title").trigger("click");
        $(this).addClass("d-none");
        $(".show_tabs").removeClass("d-none");
    });
    window.addEventListener("toast_fire", (e) => {
        if (e.detail.data.type == "success") {
            iziToast.success({
                message: e.detail.data.message,
                position: "topRight",
            });
            return;
        }
        iziToast.error({
            message: e.detail.data.message,
            position: "topRight",
        });
    });

    $(".add_address").on("click", function () {
        let name = $("#name").val();
        $(".add_address").html("Add Address");
        let type = $("#type").val();
        let mobile = $("#mobile").val();
        let alternate_mobile = $("#alternate_mobile").val();
        let address = $("#form_address").val();
        let landmark = $("#landmark").val();
        let city_list = $("#city_list").val();
        let postcode = $("#postcode").val();
        let state = $("#state").val();
        let country = $("#country_list").val();
        let latitude = $("#latitude").val();
        let longitude = $("#longitude").val();
        let address_id = $("#edit_address_id").val();
        $.ajax({
            type: "POST",
            url: appUrl + "addresses/add_address",
            data: {
                name: name,
                type: type,
                mobile: mobile,
                alternate_mobile: alternate_mobile,
                address: address,
                landmark: landmark,
                city: city_list,
                pincode: postcode,
                state: state,
                country: country,
                latitude: latitude,
                longitude: longitude,
                address_id: address_id,
            },
            success: function (response) {
                if (response.error == true) {
                    $.each(response.message, function (key, value) {
                        iziToast.error({
                            message: value[0],
                            position: "topRight",
                        });
                        return false;
                    });
                    return false;
                }
                iziToast.success({
                    message: response.message,
                    position: "topRight",
                });
                Livewire.dispatch("refreshComponent");
                $("#addNewModal").modal("hide");
            },
        });
    });
    $("#addNewModal").on("hidden.bs.modal", function () {
        $("#name").val("");
        $("#type").val("");
        $("#mobile").val("");
        $("#alternate_mobile").val("");
        $("#form_address").val("");
        $("#landmark").val("");
        $("#city_list").val("");
        $("#postcode").val("");
        $("#state").val("");
        $("#country_list").val("");
        $("#latitude").val("");
        $("#longitude").val("");
        $("#edit_address_id").val("");
    });
    $(".edit-address-btn").on("click", function () {
        var addressId = $(this).data("address-id");
        $("#edit_address_id").val(addressId);
        $.ajax({
            url: appUrl + "my-account/addresses/edit_address",
            method: "GET",
            data: { address_id: addressId },
            success: function (response) {
                var country = new Option(response.country, false, false, false);
                $("#country_list").append(country).trigger("change");

                var city = new Option(response.city, false, false, false);
                $("#city_list").append(city).trigger("change");

                $("#name").val(response.name);
                $("#type").val(response.type);
                $("#mobile").val(response.mobile);
                $("#alternate_mobile").val(response.alternate_mobile);
                $("#form_address").val(response.address);
                $("#landmark").val(response.landmark);
                $("#postcode").val(response.pincode);
                $("#state").val(response.state);
                $("#latitude").val(response.latitude);
                $("#longitude").val(response.longitude);
                $(".add_address").html("Update Address");
            },
            error: function (xhr, status, error) {
                console.error(error);
            },
        });
    });

    $(document).on("click", ".delete_address", function (e) {
        e.preventDefault();
        let address_id = $(this).attr("data-address-id");
        Swal.fire({
            title: "Do you Really want to Remove This Address?",
            showCancelButton: true,
            confirmButtonText: "Delete",
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch("deleteAddress", { address_id });
            }
        });
    });

    $(".city_list").select2({
        ajax: {
            url: appUrl + "my-account/get_Cities",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            cache: true,
        },
        dropdownParent: $(".city_list_div"),
        minimumInputLength: 1,
        placeholder: "Search for City",
    });
    // Call the function for each select element
    initializeSelect2(
        "#country_list",
        "my-account/get_Countries",
        "Search for countries",
        $(".country_list_div")
    );
    initializeSelect2(
        "#edit-country_list",
        "my-account/get_Countries",
        "Search for countries",
        $(".edit-country_list_div")
    );
    initializeSelect2(
        "#city_list",
        "my-account/get_Cities",
        "Search for City",
        $(".city_list_div")
    );
    initializeSelect2(
        "#edit-city_list",
        "my-account/get_Cities",
        "Search for City",
        $(".edit-city_list_div")
    );

    // user wallet transaction table
    let $table = $("#user_wallet_transactions");
    let $refreshButton = $("#tableRefresh");

    $refreshButton.on("click", function () {
        $table.bootstrapTable("refresh");
    });

    let $searchInput = $("#searchInput");

    $searchInput.on("input", function () {
        let searchText = $(this).val();
        $table.bootstrapTable("searchText", searchText);
    });

    $(function () {
        $("#toolbar")
            .find("select")
            .change(function () {
                $table.bootstrapTable("destroy").bootstrapTable({
                    exportDataType: $(this).val(),
                    exportTypes: [
                        "json",
                        "xml",
                        "csv",
                        "txt",
                        "sql",
                        "excel",
                        "pdf",
                    ],
                    columns: [
                        {
                            field: "state",
                            checkbox: true,
                            visible: $(this).val() === "selected",
                        },
                        {
                            field: "id",
                            title: "ID",
                        },
                        {
                            field: "name",
                            title: "Item Name",
                        },
                        {
                            field: "price",
                            title: "Item Price",
                        },
                    ],
                });
            })
            .trigger("change");
    });

    // edit profile
    initListner("submit", "#edit-profile-form", (e) => {
        e.preventDefault();

        let formData = new FormData();
        if ($("#city_list").val() == null) {
            iziToast.error({
                message: "Please Select City",
                position: "topRight",
            });
            return;
        }
        if ($("#country_list").val() == null) {
            iziToast.error({
                message: "Please Select Country",
                position: "topRight",
            });
            return;
        }
        let image = "";
        if ($("#profile_upload").val() != "") {
            image = $("#profile_upload")[0].files[0];
        }
        formData.append("username", $("#edit-username").val());
        formData.append("city", $("#city_list").val());
        formData.append("country", $("#country_list").val());
        formData.append("address", $("#edit-streetaddress").val());
        formData.append("zipcode", $("#edit-zipcode").val());
        formData.append("profile_upload", image);
        $.ajax({
            type: "POST",
            url: appUrl + "my-account/profile_update",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error == false) {
                    Livewire.dispatch("refreshComponent");
                    $("#editProfileModal").modal("hide");
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    return;
                }
                $.each(response.message, function (key, value) {
                    iziToast.error({
                        message: value[0],
                        position: "topRight",
                    });
                    return false;
                });
                return false;
            },
        });
    });

    $(".check-product-deliverability").on("click", function () {
        let product_deliverability_type = $(
            "#product_deliverability_type"
        ).val();
        let product_id = $("#product_id").val();
        let city = $("#city_list").val();
        let pincode = $("#pincode").val();
        let product_type = $("#product_type").val();
        if (product_deliverability_type == "city_wise_deliverability") {
            if (city == undefined || city == null || city == "") {
                iziToast.error({
                    message:
                        "Please Choose City to Verify the Product Deliverability",
                    position: "topRight",
                });
                return;
            }
        } else {
            if (pincode == undefined || pincode == null || pincode == "") {
                iziToast.error({
                    message:
                        "Please Enter Pincode to Verify the Product Deliverability",
                    position: "topRight",
                });
                return;
            }
        }
        $.ajax({
            type: "POST",
            url: appUrl + "check-product-deliverability",
            data: {
                product_id,
                city,
                pincode,
                product_type,
            },
            dataType: "json",
            success: function (response) {
                if (response.error == true) {
                    $.each(response.message, function (key, value) {
                        iziToast.error({
                            message: value[0],
                            position: "topRight",
                        });
                        return false;
                    });
                }
                if (response[0].is_deliverable == false) {
                    $(".deliverability-res")
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .html("Sorry, but the product cannot be delivered.");
                    return false;
                }
                $(".deliverability-res")
                    .removeClass("text-danger")
                    .addClass("text-success")
                    .html(
                        "The product is deliverable, and the delivery charges will be added during checkout."
                    );
                return false;
            },
        });
    });

    $(".AddNewTicket").on("click", function () {
        let ticket_id = $(this).data("ticket-id");
        let user_id = $("#user_id").val();
        $("#ticket_id").val(ticket_id);

        $.ajax({
            type: "post",
            url: appUrl + "my-account/support/get-ticket",
            data: {
                ticket_id,
                user_id,
            },
            success: function (response) {
                if (response.error == false) {
                    $("#ticket_type").val(response.data.ticket_type_id);
                    $("#ticket_email").val(response.data.email);
                    $("#ticket_description").val(response.data.description);
                    $("#ticket_subject").val(response.data.subject);
                    $(".add_ticket_btn").html("Update Ticket");
                }
            },
        });
    });
    $("#AddNewTicket").on("hidden.bs.modal", function () {
        $("#ticket_id").val("");
        $("#ticket_type").val("");
        $("#ticket_email").val("");
        $("#ticket_description").val("");
        $("#ticket_subject").val("");
        $(".add_ticket_btn").html("Add");
    });

    $(".add_ticket_btn").on("click", function () {
        let ticket_type = $("#ticket_type").val();
        let ticket_email = $("#ticket_email").val();
        let ticket_description = $("#ticket_description").val();
        let ticket_subject = $("#ticket_subject").val();
        let ticket_id = $("#ticket_id").val();

        $.ajax({
            type: "post",
            url: appUrl + "my-account/support/add-ticket",
            data: {
                ticket_type,
                ticket_email,
                ticket_description,
                ticket_subject,
                ticket_id,
            },
            success: function (response) {
                if (response.error == false) {
                    Livewire.dispatch("refreshComponent");
                    $("#AddNewTicket").modal("hide");
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    return;
                }
                $.each(response.message, function (key, value) {
                    iziToast.error({
                        message: value[0],
                        position: "topRight",
                    });
                    return false;
                });
                return false;
            },
        });
    });

    // change password
    $(document).on("click", ".change_password", () => {
        let current_password = $("#current_password").val();
        let new_password = $("#new_password").val();
        let verify_password = $("#verify_password").val();

        $.ajax({
            type: "POST",
            url: appUrl + "my-account/change-password",
            data: {
                current_password: current_password,
                new_password: new_password,
                verify_password: verify_password,
            },
            success: function (response) {
                if (response.error == false) {
                    Livewire.dispatch("refreshComponent");
                    $("#editLoginModal").modal("hide");
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    return;
                }
                if (typeof response.message === "string") {
                    iziToast.error({
                        message: response.message,
                        position: "topRight",
                    });
                } else {
                    $.each(response.message, function (key, value) {
                        iziToast.error({
                            message: value[0],
                            position: "topRight",
                        });
                        return false;
                    });
                }
                return false;
            },
        });
    });

    $(".update_order_item_status").on("click", function () {
        let order_status = $(this).data("status");
        let initial_order_item_id = $(this).data("item-id");
        let confirm_title = "";
        let confirm_btn = "";

        if (order_status === "cancelled") {
            confirm_title = "Are you sure you want to cancel the selected ordered items?";
            confirm_btn = "Yes Remove";

            $('#refundOptionModal').modal('show');
            $(`.cancel-item-checkbox[data-item-id="${initial_order_item_id}"]`).prop('checked', true);

            // Налаштування варіантів повернення на основі методу оплати
            const paymentMethod = $('#paymentMethod').val();
            const paymentType = $('#paymentType').val();

            if (paymentMethod === 'wallet') {
                $('#refundWallet').prop('checked', true);
                $('#refundCard').prop('disabled', true);
            } else if (paymentMethod === 'transaction' && paymentType === 'stripe') {
                $('#refundWallet').prop('checked', true); // За замовчуванням вибрано гаманець
                $('#refundCard').prop('disabled', false); // Обидва варіанти доступні для Stripe
            } else {
                $('#refundWallet').prop('checked', true);
                $('#refundCard').prop('disabled', true); // Для інших платіжних систем тільки гаманець
            }

            $('#confirmRefund').off('click').on('click', function () {
                const refundMethod = $('input[name="refundMethod"]:checked').val();
                const selectedItems = $('.cancel-item-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedItems.length === 0) {
                    iziToast.error({
                        message: "Please select at least one item to cancel",
                        position: "topRight",
                    });
                    return;
                }

                // Перевірка для wallet: дозволяємо тільки повернення на гаманець
                if (paymentMethod === 'wallet' && refundMethod !== 'wallet') {
                    iziToast.error({
                        message: "For wallet payments, refund can only be made to wallet",
                        position: "topRight",
                    });
                    return;
                }

                // Для transaction+stripe дозволяємо обидва варіанти, для інших тільки wallet
                if (paymentMethod === 'transaction' && paymentType !== 'stripe' && refundMethod !== 'wallet') {
                    iziToast.error({
                        message: "For this payment method, refund can only be made to wallet",
                        position: "topRight",
                    });
                    return;
                }

                Swal.fire({
                    title: confirm_title,
                    showCancelButton: true,
                    confirmButtonText: confirm_btn,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: appUrl + "orders/update-order-item-status",
                            data: {
                                order_status: order_status,
                                order_item_id: selectedItems,
                                refund_method: refundMethod
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.error == false) {
                                    iziToast.success({
                                        message: response.message,
                                        position: "topRight",
                                    });
                                    Livewire.dispatch("refreshComponent");
                                    $('#refundOptionModal').modal('hide');
                                    onTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    iziToast.error({
                                        message: response.message,
                                        position: "topRight",
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                iziToast.error({
                                    message: "An error occurred while processing your request",
                                    position: "topRight",
                                });
                            }
                        });
                    }
                });
            });
        } else if (order_status === "returned") {
            // Keep existing return logic
            confirm_title = "Are you sure you want to return the ordered item?";
            confirm_btn = "Yes Return";

            Swal.fire({
                title: confirm_title,
                showCancelButton: true,
                confirmButtonText: confirm_btn,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: appUrl + "orders/update-order-item-status",
                        data: {
                            order_status,
                            order_item_id: initial_order_item_id,
                        },
                        dataType: "json",
                        success: function (response) {
                            if (response.error == false) {
                                iziToast.success({
                                    message: response.message,
                                    position: "topRight",
                                });
                                Livewire.dispatch("refreshComponent");
                            } else {
                                iziToast.error({
                                    message: response.message,
                                    position: "topRight",
                                });
                            }
                        },
                    });
                }
            });
        }
    });

    $(".chat-btn-popup").on("click", function () {
        $("#chat-iframe").toggleClass("chat-iframe-show");
    });

    $(function () {
        $("#number").intlTelInput({
            allowExtensions: !0,
            formatOnDisplay: !0,
            autoFormat: !0,
            autoHideDialCode: !0,
            autoPlaceholder: !0,
            defaultCountry: "in",
            ipinfoToken: "yolo",
            nationalMode: !1,
            numberType: "MOBILE",
            preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
            preventInvalidNumbers: !0,
            separateDialCode: !0,
            initialCountry: "auto",
            geoIpLookup: function (e) {
                $.get("https://ipinfo.io", function () { }, "jsonp").always(
                    function (t) {
                        var a = t && t.country ? t.country : "";
                        e(a);
                    }
                );
            },
            utilsScript:
                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js",
        });
    });

    let authentication_method = $("#authentication_method").val();
    if (authentication_method == "firebase") {
        FirebaseAuth();
    } else if (authentication_method == "telegram") {
        TelegramAuth();
    } else if (authentication_method == "sms") {
        CustomSmsAuth();
    }

    // let store = setUrlParameter(custom_url, "store", store_slug);
    let store = custom_url;
    let split_url = custom_url.split("?");
    const urlParams = new URLSearchParams("?".concat(split_url[1]));
    const store_exsist = urlParams.get("store");
    // if (store_exsist == null) {
    //     Livewire.navigate(store);
    // }


    const send_dealer_request_form = document.querySelector("#dealer-form");
    if (send_dealer_request_form) {
        send_dealer_request_form.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const first_name = formData.get("first_name");
            const last_name = formData.get("last_name");
            const dealer_birthdate = formData.get("dealer_birthdate");
            const message = formData.get("message");
            if (first_name == "" || last_name == "" || dealer_birthdate == "") {
                iziToast.error({
                    message: "Please fill all the fields",
                    position: "topRight",
                });
                return false;
            }
            $(".send_dealer_request").html("Sending...");
            $(".send_dealer_request").attr("disabled", true);
            $.ajax({
                type: "POST",
                url: appUrl + "my-account/user-status/send_dealer_request",
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    dealer_birthdate: dealer_birthdate,
                    message: message,
                },
                success: function (response) {
                    if (response.error == true) {
                        $.each(response.message, function (key, value) {
                            iziToast.error({
                                message: value[0],
                                position: "topRight",
                            });
                            return false;
                        });
                        return false;
                    }
                    iziToast.success({
                        message: response.message,
                        position: "topRight",
                    });
                    Livewire.dispatch("refreshComponent");
                    $("#dealerModal").modal("hide");
                },
            });
        });
        window.dealer_form_initialized = true;
    }

    const send_manager_request_form = document.querySelector("#manager-form");
    if (send_manager_request_form) {
        send_manager_request_form.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const first_name = formData.get("first_name");
            const last_name = formData.get("last_name");
            const birthdate = formData.get("birthdate");
            if (first_name == "" || last_name == "" || birthdate == "") {
                iziToast.error({
                    message: "Please fill all the fields",
                    position: "topRight",
                });
                return false;
            }
            $(".send_manager_request").html("Sending...");
            $.ajax({
                type: "POST",
                url: appUrl + "my-account/user-status/send_manager_request",
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    birthdate: birthdate,
                },
                success: function (response) {
                    if (response.error == true) {
                        $.each(response.message, function (key, value) {
                            iziToast.error({
                                message: value[0],
                                position: "topRight",
                            });
                            return false;
                        });
                        return false;
                    }
                    iziToast.success({
                        message:
                            response.message +
                            " " +
                            first_name +
                            " " +
                            last_name +
                            " " +
                            birthdate,
                        position: "topRight",
                    });
                    Livewire.dispatch("refreshComponent");
                    $("#managerModal").modal("hide");
                },
            });
        });
    }

});