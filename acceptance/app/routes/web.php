<?php
\App::route()->get()->url( 'hook' )->handle( function () { return 'acceptance:/hook'; } );

\App::route()->all();
