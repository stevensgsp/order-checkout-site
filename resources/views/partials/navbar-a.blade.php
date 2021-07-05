<a class="nav-link {{ Request::route()->getName() == $routeName ? 'active' : '' }}" href="{{ route($routeName) }}">{{ $title }}</a>
