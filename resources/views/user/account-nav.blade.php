<ul class="account-nav">
    <li><a href="{{ route('user.index') }}" class="menu-link">Dashboard</a></li>
    <li><a href="{{route('user.account.orders')}}" 
           class="menu-link {{Route::is('user.account.orders') ? 'active':''}}">
           Orders</a></li>
    <li><a href="account-address.html" class="menu-link">Addresses</a></li>
    <li><a href="account-details.html" class="menu-link">Account Details</a></li>
    <li><a href="account-wishlist.html" class="menu-link">Wishlist</a></li>

    <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
    <li>
        <a href="{{ route('logout') }}" class="menu-link logout"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
           Log Out
        </a>
    </li>
</ul>
