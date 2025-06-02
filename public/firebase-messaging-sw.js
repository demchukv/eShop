// Імпорт Firebase SDK для Service Worker
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Ініціалізація Firebase
firebase.initializeApp({
    apiKey: "AIzaSyDVk8kCeQwuyJ2Xv89V4BGPp_wqM09YJhw",
    authDomain: "eshop-8c86d.firebaseapp.com",
    projectId: "eshop-8c86d",
    databaseURL: "https://eshop-8c86d-default-rtdb.europe-west1.firebasedatabase.app/",
    storageBucket: "eshop-8c86d.firebasestorage.app",
    messagingSenderId: "59427557741",
    appId: "1:59427557741:web:c281f17c5754d8fef4ca64",
    measurementId: "retreg35"
});

// Ініціалізація Messaging
const messaging = firebase.messaging();