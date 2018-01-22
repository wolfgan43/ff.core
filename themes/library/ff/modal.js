ff.modal = (function () {
    //privates var and functions
    var __ff        = false;
    var dialogs		= ff.hash(); //contenitore dei tutte le modali
    var framework_css_conversion = {
        "base" : {

        },
        "bootstrap" : {
            "overlay"       : "modal",
            "window"        : "modal-dialog",
            "window-small"  : "modal-sm",
            "window-medium" : "modal-md",
            "window-large"  : "modal-lg",
            "window-huge"   : "modal-full",
            "inner-wrap"    : "modal-content",
            "header"        : "modal-header",
            "header-title"  : "modal-header",
            "content"       : "modal-body",
            "footer"        : "modal-footer",
            "button"        : "close",
            "effect"        : "fade"
        },
        "foundation" : {
            "overlay"       : "xxx", //da trovare il corrispettivo in foundation
            "window"        : "xxx", //da trovare il corrispettivo in foundation
            "window-small"  : "xxx", //da trovare il corrispettivo in foundation
            "window-medium" : "xxx", //da trovare il corrispettivo in foundation
            "window-large"  : "xxx", //da trovare il corrispettivo in foundation
            "window-huge"   : "xxx", //da trovare il corrispettivo in foundation
            "inner-wrap"    : "xxx", //da trovare il corrispettivo in foundation
            "header"        : "xxx", //da trovare il corrispettivo in foundation
            "header-title"  : "xxx", //da trovare il corrispettivo in foundation
            "content"       : "xxx", //da trovare il corrispettivo in foundation
            "footer"        : "xxx", //da trovare il corrispettivo in foundation
            "button"        : "xxx", //da trovare il corrispettivo in foundation
            "effect"        : "xxx"  //da trovare il corrispettivo in foundation
        }
    };
    var font_icon_conversion = {
        "fontawesome" : {
            "resize"        : "expand"
            , "close"       : "fa fa-times"
        }
    };


    var framework_css = {
        "overlay" : {
            "class"         : null,
            "dialog"        : ["overlay", "effect"]
        },
        "window" : {
            "class"         : null,
            "dialog"        : ["window", "window-medium"]
        },
        "inner-wrap" : {
            "class"         : null,
            "dialog"        : "inner-wrap"
        },
        "header" : {
            "class"         : null,
            "dialog"        : "header"
        },
        "header-title" : {
            "class"         : null,
            "dialog"        : "header-title"
        },
        "content" : {
            "class"         : null,
            "dialog"        : "content"
        },
        "footer" : {
            "class"         : null,
            "dialog"        : "footer"
        }
    };
    /**
     @effect (String or false)
     Slide, Fade, ecc or false

     set By params or by response
     */
    var effect              = false;
    /**
     @width (Object Or Number Or null)
     Object: Basato sul grid system
     {
        "xs" : [0-12]
        , "sm" : [0-12]
        , "md" : [0-12]
        , "lg" : [0-12]
     }
     Number : in pixel

     set By params or by response
     */
    var width               =  null;
    /**
     @blockUI (Boolean or null)
     Block user iteraction with overlay in background.

     set By params or by response
     */
    var blockUI             = true;
    /**
     @title (String or null)
     The title display in the head of modal.

     set By params or by response
     */
    var title               = null;
    /**
     @description (String or null)
     Optional description

     set By params or by response
     */
    var description         = null;
    /**
     @tabs (String or null)
     Optional Tabs for switch panels inside dialog.

     set By params or by response
     */
    var tabs = {};

    var buttons = {
        "header" : {
            "close" : {
                "label"             : ""
                , "placeholder"     : "Chiudi"
                , "icon"            : "close"
                , "display"         : true
                , "class"           : {
                    "util"          : ["align-right"]
                }
                , "fixed"           : false //override che permette di posizionare un bottone al di fuori della dialog. Valori possibili: top-left top-right bottom-left bottom-right
            },
            "resize" : {
                "label"             : ""
                , "placeholder"     : "Ridimensiona"
                , "icon"            : "close"
                , "display"         : false
                , "class"           : {
                    "util"          : ["align-right"]
                }
                , "fixed"           : false //override che permette di posizionare un bottone al di fuori della dialog. Valori possibili: top-left top-right bottom-left bottom-right
            }
        },
        "footer" : {
            "insert" : {
                //[...] Bottoni di azione defninibili da parametri o anche da response
            },
            "update" : {
                //[...] Bottoni di azione defninibili da parametri o anche da response
            },
            "delete" : {
                //[...] Bottoni di azione defninibili da parametri o anche da response
            },
            "close" : {
                //[...] Bottoni di azione defninibili da parametri o anche da response
            }
        }
    };


    var template = '<div id="ffWidget_dialog_[id]" class="[overlay]" role="dialog">'
        +  '<div class="[window]">'
        +    '<div class="[inner-wrap]">'
        +      '<div class="[header]">'
        +           '{buttons.header}'
        +        '<div class="[header-title]">{title}{description}</div>'
        +           '{tabs}'
        +      '</div>'
        +      '<div id="ffWidget_dialog_container_[id]" class="[content]">{content}</div>'
        +      '<div class="[footer]">{footer}{buttons.footer}</div>'
        +    '</div>'
        +  '</div>'
        + '</div>';

    var display = {
        "modal" : function() {
            //richiama tutti il css di riferimento  con ff.injectCss definisce e processa la struttura html necessaria
        },
        "sidebar" : function () {
            //richiama tutti il css di riferimento  con ff.injectCss definisce e processa la struttura html necessaria
        }
    };

    var processTabs = function() {

    };
    var processButtons = function() {

    };
    var makeInstance = function(id) {
        return template
            .replace("[overlay]", "")
            .replace("[window]", "")
            //[...]
    };

    var that = { // publics var and functions
        "init" : function(component) {
        },
        "get" : function (id) {
            return dialogs.get(id);
        },
        "addDialog" : function (params) {
            /*
            dialogs[params.id] = {
                "title" : params.title || "",
                "description" : params.description || "",
                "display" : params.display || "modal",
                [...]
            }*/
        },
        "addTabs": function (id, tabs) {
            var tab = { //example di params
                "label"             : "",//far apparire la scritta
                "placeholder"       : "", //placeholder che appare sulla icona eventuale
                "icon"              : "", //aggiungere una icona sempre presa dal fontIcon
                "close"             : false, //se visualizzare o meno il bottone chiudi
                "content"           : "" // selettore o porzione html
            };
        },
        "addButtons": function (id, buttons) {
            var button = { //example di params
                "label"             : ""
                , "placeholder"     : "Chiudi"
                , "icon"            : "close"
                , "display"         : true
                , "class"           : {
                    "util"          : ["align-right"]
                }
                , "fixed"           : false //override che permette di posizionare un bottone al di fuori della dialog. Valori possibili: top-left top-right bottom-left bottom-right
                , "positon"         : "header" //header || footer
            };
        },
        "doOpen": function (id, url, params) {
            //example usage class ff.ajax
            /*
            ff.ajax.doRequest({
                "url"                : that.parseUrl(id, url),
                "component"        : params.component,
                "section"            : params.section,
                "fields"            : fields,
                "callback"            : that.onSuccess,
                "customdata"        : {
                    "id"            : id
                    , "callback"    : params.callback
                    , "caller" : {
                        "func" : ff.ffPage.dialog.doRequest
                        , "args" : ff.argsAsArray(arguments)
                    }
                },
                "injectid"            : params.injectid,
                "dialog"            : id,
                "chainupdate"        : params.chainupdate,
                "doredirects"        : dialogs.get(id).params.doredirects
            });*/


        },
        "goToUrl" : function (id, url) {
            initsReset(id);

            dialogs.get(id).params.current_url = url;

            var fields = [
                {name: "XHR_DIALOG_ID", value: id}
            ];
            ff.ajax.doRequest({
                "url"                : that.parseUrl(id, dialogs.get(id).params.current_url),
                "type"                : "GET",
                "fields"            : fields,
                "callback"            : that.onSuccess,
                "customdata"        : {
                    "id" : id
                    , "caller" : {
                        "func" : ff.ffPage.dialog.goToUrl
                        , "args" : ff.argsAsArray(arguments)
                    }
                },
                "injectid"            : dialogs.get(id).instance,
                "dialog"            : id,
                "doredirects"        : dialogs.get(id).params.doredirects
            });
        },
        "onSuccess" : function (data, customdata) {

        },
        "onClose" : function (id, hide) {

        },
        "doRequest" : function (id, params) {

        },
        "doAction" : function (id, action) {

        },
        "parseUrl" : function (id, url) {
            var parsedurl = url;

            var regTags = /\[\[([a-zA-Z0-9_\-\[\](?!\]))]+)\]\]/g;
            var ret;
            while ((ret = regTags.exec(url)) !== null) {
                var tmp = ret[1].replace(/\[/g, "\\[").replace(/\]/g, "\\]");
                var encodeTmp = false;

                if(tmp.indexOf("_ENCODE") > 0) {
                    tmp = tmp.replace("_ENCODE", "");
                    encodeTmp = true;
                }

                if(tmp.indexOf("_TEXT") > 0) {
                    if(jQuery("#" + tmp.replace("_TEXT", "")).is("select")) {
                        parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp.replace("_TEXT", "") + " option:selected").text()) : jQuery("#" + tmp.replace("_TEXT", "") + " option:selected").text()), "g");
                    } else {
                        parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp.replace("_TEXT", "")).text()) : jQuery("#" + tmp.replace("_TEXT", "")).text()), "g");
                    }
                } else {
                    parsedurl = parsedurl.replace(ret[0], (encodeTmp ? encodeURIComponent(jQuery("#" + tmp).val()) : jQuery("#" + tmp).val()), "g");
                }
            }

            return parsedurl;
        }
    }; // publics' end
    return that;
})();



