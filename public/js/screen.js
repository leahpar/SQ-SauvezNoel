
$(function() {

    let messages_ul = $("#messages");
    let compteur_el = $("#compteur");
    let message_template = $("template[data-type=message]").html();

    /**
     * Suppression auto des anciens messages
     */

    setInterval(function() {
        if (messages_ul.children().length > 1) {
            messages_ul.children(":first").remove();
        }
    }, 10000);

    /**
     * SSE init
     */

    if (typeof(EventSource) !== "undefined") {
        let es = new EventSource("/screen/sse");

        let listener = function (event) {
            if (typeof event.data !== 'undefined') {
                console.log(event);
                let data = JSON.parse(event.data);
                console.log(data);
                handleSSE(data);
            }
        };

        es.addEventListener("open", listener);
        es.addEventListener("message", listener);
        es.addEventListener("error", listener);
    }
    else {
        alert("Whoops! Your browser doesn't receive server-sent events.");
    }

    /**
     * SSE event
     */

    function handleSSE(data) {
        //console.log("data", data);

        $.each(data["messages"], function(idx, message) {
            //console.log("message", message);
            messages_ul.append(message_template
                .replace("__nom__", message.nom)
                .replace("__message__", message.message)
            );
        });

        compteur_el.text(data["count"] + 124);
    }
    
    /*$("#messages li").each(function(index){
        $(this).delay(index*20000).animate({left:"100vw"},20000);
    });*/
    
    

});
