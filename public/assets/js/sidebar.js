"use strict";

var adminMenu = function adminMenu()
{
    var hamburger = document.querySelector('.hamburger');
    var menu = document.querySelector('.sidebar-menu');
    var profile = document.querySelector('.sidebar-profileContainer');
    var adminMenuItems = document.querySelectorAll('.sidebar-item');
    hamburger.addEventListener('click', function () {
        /*menu.classList.toggle('sidebar-menu-active');
        profile.classList.toggle('sidebar-profileContainer-active');*/
        if (menu.classList.contains('sidebar-menu-active')) {
            menu.classList.remove('sidebar-menu-active');
            menu.classList.add('sidebar-menu-inactive');
        } else {
            menu.classList.remove('sidebar-menu-inactive');
            menu.classList.add('sidebar-menu-active');
        }

        if (profile.classList.contains('sidebar-profileContainer-active')) {
            profile.classList.remove('sidebar-profileContainer-active');
            profile.classList.add('sidebar-profileContainer-inactive');
        } else {
            profile.classList.remove('sidebar-profileContainer-inactive');
            profile.classList.add('sidebar-profileContainer-active');
        }

        hamburger.classList.toggle('is-active');
    });
    /*
    adminMenuItems.forEach((item, index) => {
        item.style.animation = `adminMenuFade 0.5s ease forwards ${index / 7}s`;
    });
    */
    //burger.classList.toggle('toggle');
};

// adminMenu();
