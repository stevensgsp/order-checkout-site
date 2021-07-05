<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarText"
            aria-controls="navbarText"
            aria-expanded="false"
            aria-label="Toggle navigation"
        ><span class="navbar-toggler-icon"></span></button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    @include('partials.navbar-a', ['routeName' => 'home', 'title' => 'Home'])
                </li>
                <li class="nav-item">
                    @include('partials.navbar-a', ['routeName' => 'orders.show-store', 'title' => 'Store'])
                </li>
                <li class="nav-item">
                    @include('partials.navbar-a', [
                        'routeName' => 'orders.index',
                        'title' => 'List of orders (do not enter if you are not admin, pretty please.)'
                    ])
                </li>
            </ul>
            <span class="navbar-text">
                The new gen of single product stores!
            </span>
        </div>
    </div>
</nav>
