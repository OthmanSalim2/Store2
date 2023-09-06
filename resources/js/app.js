import Echo from "laravel-echo";
import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

//Echo.channel this's mean public channel.
// var channel = Echo.channel(`App.Model.User.${user_id}`);
var channel = Echo.private(`App.Model.User.${user_id}`);
// .my-event if was without dot(.) laravel supported app.Event.my-event but put dot won't the path of event.
// data here it represent the data sent to pusher server.
channel.notification(".my-event", function (data) {
    console.log(data); // to test just.
    alert(data);
    // alert(JSON.stringify(data));
});

// channel.listen(".my-event", function (data) {
//     alert(JSON.stringify(data));
// });
