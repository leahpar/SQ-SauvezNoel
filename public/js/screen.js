
$(function() {

    let messages_ul = $("#messages");
    let compteur_el = $("#compteur");
    let message_template = $("#message_template").html();

    /**
     * Suppression auto des anciens messages
     */

    setInterval(function() {
        //if (messages_ul.children().length > 5) {
            messages_ul.children(":first").remove();
        //}
    }, 10000);

    /**
     * Mise à jour du compteur de messages
     */
    function setCompteur(cpt) {
        // Compteur honnête
        compteur_el.text(cpt);

        // Compteur menteur
        //compteur_el.text(342 + cpt);
    }

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

        setCompteur(data["count"]);
    }

});
