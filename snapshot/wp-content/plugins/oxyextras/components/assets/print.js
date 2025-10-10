var PrintElements = (function () {
    "use strict";

    var _hide = function (element) {
       
        if (!element.classList.contains("x-print-preserve-print")) {
            element.classList.add("x-print-no-print");
        }
    };

    var _preserve = function (element, isStartingElement) {
        element.classList.remove("x-print-no-print");
        element.classList.add("x-print-preserve-print");
        if (!isStartingElement) {
            element.classList.add("x-print-preserve-ancestor");
        }
    };

    var _clean = function (element) {
        element.classList.remove("x-print-no-print");
        element.classList.remove("x-print-preserve-print");
        element.classList.remove("x-print-preserve-ancestor");
    };

    var _walkSiblings = function (element, callback) {
        var sibling = element.previousElementSibling;
        while (sibling) {
            callback(sibling);
            sibling = sibling.previousElementSibling;
        }
        sibling = element.nextElementSibling;
        while (sibling) {
            callback(sibling);
            sibling = sibling.nextElementSibling;
        }
    };

    var _attachPrintClasses = function (element, isStartingElement) {
        _preserve(element, isStartingElement);
        _walkSiblings(element, _hide);
    };

    var _cleanup = function (element, isStartingElement) {
        _clean(element);
        _walkSiblings(element, _clean);
    };

    var _walkTree = function (element, callback) {
        var currentElement = element;
        callback(currentElement, true);
        currentElement = currentElement.parentElement;
        while (currentElement && currentElement.nodeName !== "BODY") {
            callback(currentElement, false);
            currentElement = currentElement.parentElement;
        }
    };

    var _print = function (elements) {
        for (var i = 0; i < elements.length; i++) {
            _walkTree(elements[i], _attachPrintClasses);
        }
        window.print();

        setTimeout(() => {
            for (i = 0; i < elements.length; i++) {
                _walkTree(elements[i], _cleanup);
            }
          }, 1000);
        
    };

    return {
        print: _print
    };
})();


function xSocialSharePrint() {

    const extrasSocialSharePrint = function ( container ) {

        container.querySelectorAll(".oxy-social-share-buttons").forEach((socialShare) => {

            let printEl = document.body

            if ( 'enable' === socialShare.querySelector('.oxy-social-share-buttons_data').getAttribute('data-hide-print') ) {
                socialShare.setAttribute('data-x-hide-print','true')
            }

            socialShare.querySelectorAll(".oxy-share-button.print").forEach((printLink) => {

                if ( printLink.getAttribute('data-print-exact') ) {
                    document.documentElement.style.setProperty('print-color-adjust', printLink.getAttribute('data-print-exact'));
                    document.documentElement.style.setProperty('-webkit-print-color-adjust', printLink.getAttribute('data-print-exact'));
                }

                printLink.addEventListener('click', (e) => {

                    e.preventDefault();

                     let printSelector = printLink.getAttribute('data-print-selector')

                    if ( printSelector ) {
                        printEl = document.querySelector(printSelector);
                    }

                    if ( printEl ) {
                        if ( printLink.closest('.oxy-dynamic-list > .ct-div-block') ) {
                            if ( printLink.closest('.oxy-dynamic-list > .ct-div-block').querySelector(printSelector) ) {
                                printEl = printLink.closest('.oxy-dynamic-list > .ct-div-block').querySelector(printSelector)
                            }
                        }

                        PrintElements.print([printEl]);
                    } else {
                        PrintElements.print([document.body]);
                        console.log('OxyExtras: Element to print not found, check selector is correct')
                    }

                    
                })

            })

        })

    }

    extrasSocialSharePrint(document);

    // Expose function
    window.doExtrasSocialSharePrint = extrasSocialSharePrint;

}

document.addEventListener("DOMContentLoaded",function(e){
    xSocialSharePrint()
});