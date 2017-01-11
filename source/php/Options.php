<?php

namespace ContentTranslate;

class Options
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'addOptionsPage'));
    }

    public function addOptionsPage()
    {
        add_menu_page(
            __('Languages', 'wp-content-translator'),
            __('Languages', 'wp-content-translator'),
            'edit_posts',
            'languages',
            function () {},
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIxLjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyNTUgMTQzIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyNTUgMTQzOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPHBhdGggZD0iTTE3MS40LDQuMWgzMS44bDQ3LjYsMTM0LjdoLTMwLjVsLTguOS0yNy43aC00OS42bC05LjEsMjcuN2gtMjkuNEwxNzEuNCw0LjF6IE0xNjkuNSw4Ny45SDIwNGwtMTctNTNMMTY5LjUsODcuOXoiLz4KPC9nPgo8cmVjdCB4PSIzNC42IiB5PSI0LjEiIHdpZHRoPSI5Ni4zIiBoZWlnaHQ9IjE5LjMiLz4KPGc+Cgk8cGF0aCBkPSJNMTExLDEzOC45bDYuOS0xOS4zaC0xNS44VjYxLjloMzYuNGw2LjktMTkuM2gtMTMwdjE5LjNoMzguNGMtMC45LDIwLjctNy4xLDUwLjgtNDkuNiw1Ny45bDMuMiwxOQoJCWM1Ni45LTkuNSw2NC42LTUzLjEsNjUuNy03Ni45aDkuOHY3Ny4xSDExMXoiLz4KPC9nPgo8L3N2Zz4K',
            100
        );
    }


}
