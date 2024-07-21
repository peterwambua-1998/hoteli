<nav class="sidebar ">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      <span style="color:#fbbc06;font-weight:bold">Kisi</span><span style="color: green;font-weight:bold">mani</span>
    </a>
    <div id="close-sidebar" class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  
  <div class="sidebar-body">
    <ul class="nav">
      {{--- admin dashboard ---}}
      <li class="nav-item nav-category">Main</li>
      @if (Auth::user()->role == 1)
      
      {{-- dashboard --}}
      <li class="nav-item {{ active_class(['home']) }}">
        <a href="{{ url('home') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Dashboard</span>
        </a>
      </li>



      <li class="nav-item {{ active_class(['system/day']) }} {{ active_class(['collected/cash']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#day" role="button" aria-expanded="{{ is_active_route(['collected/cash']) }} {{ is_active_route(['system/day']) }}" aria-controls="day">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Days</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['system/day']) }} {{ show_class(['collected/cash']) }}" id="day">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('system.days.index')}}" class="nav-link {{ active_class(['system/day']) }}">Current Day</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('collected.cash') }}" class="nav-link {{ active_class(['collected/cash']) }}">Previous Days</a>
            </li>
          </ul>
        </div>
      </li>

      

      <li class="nav-item {{ active_class(['meal-plan']) }} {{ active_class(['booking']) }} {{ active_class(['packages']) }} {{ active_class(['rooms']) }} {{ active_class(['reservations']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#acc" role="button" aria-expanded="{{ is_active_route(['booking']) }} {{ is_active_route(['reservations']) }} {{ is_active_route(['packages']) }} {{ is_active_route(['rooms']) }} {{ is_active_route(['meal-plan']) }}" aria-controls="acc">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Accommodation</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['meal-plan']) }} {{ show_class(['packages']) }} {{ show_class(['booking']) }} {{ show_class(['rooms']) }} {{ show_class(['reservations']) }}" id="acc">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('reservations.index')}}" class="nav-link {{ active_class(['reservations']) }}">Bookings</a>
            </li>
            <li class="nav-item">
              <a href="{{route('b.index')}}" class="nav-link {{ active_class(['booking']) }}">Reservations</a>
            </li>
            <li class="nav-item">
              <a href="{{route('rooms.index')}}" class="nav-link {{ active_class(['rooms']) }}">Rooms</a>
            </li>
            <li class="nav-item">
              <a href="{{route('packages.index')}}" class="nav-link {{ active_class(['packages']) }}">Packages</a>
            </li>
            {{-- <li class="nav-item">
              <a href="{{route('meal-plan.index')}}" class="nav-link {{ active_class(['meal-plan']) }}">Meal Plans</a>
            </li> --}}
          </ul>
        </div>
      </li>



      <li class="nav-item {{ active_class(['accounts']) }}  {{ active_class(['accounts/*']) }}">
        <a href="{{ url('accounts') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Customers</span>
        </a>
      </li>

      <li class="nav-item {{ active_class(['bank-account']) }}">
        <a href="{{ url('bank-account') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Bank Accounts</span>
        </a>
      </li>
      
      {{-- <li class="nav-item {{ active_class(['departments']) }}">
        <a href="{{ url('departments') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Departments</span>
        </a>
      </li> --}}

      <li class="nav-item {{ active_class(['reviews/show']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#attendance" role="button" aria-expanded="{{ is_active_route(['refund-request/*']) }}" aria-controls="attendance">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Accounting Dpt</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['refund-request/*']) }}" id="attendance">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('refund-request.index')}}" class="nav-link {{ active_class(['refund-request/*']) }}">Refund Requests</a>
            </li>
            {{-- <li class="nav-item">
              <a href="{{ route('absenttodaystd') }}" class="nav-link {{ active_class(['attendances/absent-today']) }}">Absent Today</a>
            </li> --}}
          </ul>
        </div>
      </li>

      <li class="
        nav-item 
        {{ active_class(['goods-receive']) }} 
        {{ active_class(['goods-receive/*']) }} 
        {{ active_class(['purchase-order']) }} 
        {{ active_class(['purchase-order/create']) }} 
        {{ active_class(['suppliers']) }} 
        {{ active_class(['bill']) }}"
      >
        <a class="nav-link" data-bs-toggle="collapse" href="#proc" role="button" aria-expanded="{{ is_active_route(['products/*','category/*', 'bill/*']) }}" aria-controls="proc">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Procurement</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['goods-receive']) }} {{ show_class(['goods-receive/*']) }} {{ show_class(['purchase-order']) }} {{ show_class(['suppliers']) }} {{ show_class(['bill']) }}" id="proc">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{ route('suppliers.index') }}" class="nav-link {{ active_class(['suppliers']) }}">Suppliers</a>
            </li>
            <li class="nav-item">
              <a href="{{route('purchase.order.index')}}" class="nav-link {{ active_class(['purchase-order']) }}">Purchase Order</a>
            </li>
            <li class="nav-item">
              <a href="{{route('goods.receive.index')}}" class="nav-link {{ active_class(['goods-receive']) }} {{ active_class(['goods-receive/*']) }}">Goods Receive Note</a>
            </li>
            <li class="nav-item">
              <a href="{{route('bill.index')}}" class="nav-link {{ active_class(['bill']) }}">Bills</a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item 
      {{ active_class(['main-store']) }} 
      {{ active_class(['maintenance-store']) }}
      {{ active_class(['main-store/requisitions']) }}
      ">
        <a class="nav-link" data-bs-toggle="collapse" href="#store" role="button" aria-expanded="{{ is_active_route(['main-store']) }} {{ is_active_route(['main-store/requisitions']) }}" aria-controls="store">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Main store</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['main-store']) }} {{ show_class(['main-store/requisitions']) }}" id="store">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('main.store.index')}}" class="nav-link {{ active_class(['main-store']) }}">Main Store</a>
            </li>
           
           
            {{-- <li class="nav-item">
              <a href="{{route('maintenance.store.index')}}" class="nav-link {{ active_class(['maintenance-store']) }}">Maintenance Store</a>
            </li> --}}
            <li class="nav-item">
              <a href="{{route('main.store.requisition')}}" class="nav-link {{ active_class(['main-store/requisitions']) }}">Material Requisitions</a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item {{ active_class(['products/*', 'category/*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#products" role="button" aria-expanded="{{ is_active_route(['products/*','category/*']) }}" aria-controls="products">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Items</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['products/*']) }}" id="products">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('products.index')}}" class="nav-link {{ active_class(['products/*']) }}">Items</a>
            </li>
            <li class="nav-item">
              <a href="{{ route('category.index') }}" class="nav-link {{ active_class(['category/*']) }}">Item category</a>
            </li>
          </ul>
        </div>
      </li>


      <li class="nav-item {{ active_class(['bar-store']) }} {{ active_class(['bar-store/orders']) }} {{ active_class(['bar-requisition']) }} {{ active_class(['bar-store/pos']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#requisition" role="button" aria-expanded="{{ is_active_route(['bar-store/orders']) }} {{ is_active_route(['bar-requisition']) }} {{ is_active_route(['bar-store/pos']) }}" aria-controls="requisition">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Bar</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['bar-store']) }} {{ show_class(['bar-store/orders']) }} {{ show_class(['bar-requisition']) }} {{ show_class(['bar-store/pos']) }}" id="requisition">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('select.pos')}}" class="nav-link {{ active_class(['bar-store/pos']) }}">POS</a>
            </li>
            <li class="nav-item">
              <a href="{{route('bar.store.index')}}" class="nav-link {{ active_class(['bar-store']) }}">Bar Store</a>
            </li>
            <li class="nav-item">
              <a href="{{route('bar.orders')}}" class="nav-link {{ active_class(['bar-store/orders']) }}">Bar Orders</a>
            </li>
            <li class="nav-item">
              <a href="{{route('bar.requisition.view')}}" class="nav-link {{ active_class(['bar-requisition']) }}">Material Requisition</a>
            </li>
          </ul>
        </div>
      </li>


      <li class="nav-item {{ active_class(['kitchen-requisition']) }} {{ active_class(['kitchen-store/pos']) }} {{ active_class(['kitchen-store/orders']) }} {{ active_class(['kitchen-store']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#kitchen" role="button" aria-expanded="{{ is_active_route(['kitchen-store/pos']) }} {{ is_active_route(['kitchen-store']) }} {{ is_active_route(['kitchen-requisition']) }}" aria-controls="kitchen">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Kitchen</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['kitchen-requisition']) }} {{ show_class(['kitchen-store/pos']) }} {{ show_class(['kitchen-store/orders']) }}  {{ show_class(['kitchen-store']) }}" id="kitchen">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('select.pos')}}" class="nav-link {{ active_class(['kitchen-store/pos']) }}">POS</a>
            </li>
            <li class="nav-item">
              <a href="{{route('kitchen.store.index')}}" class="nav-link {{ active_class(['kitchen-store']) }}">Kitchen Store</a>
            </li>
            <li class="nav-item">
              <a href="{{route('kitchen.orders')}}" class="nav-link {{ active_class(['kitchen-store/orders']) }}">Kitchen Orders</a>
            </li>
            <li class="nav-item">
              <a href="{{route('kitchen.requisition.view')}}" class="nav-link {{ active_class(['kitchen-requisition']) }}">Material Requisition</a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item {{ active_class(['account/approval']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#approvals" role="button" aria-expanded="{{ is_active_route(['account/approval']) }}" aria-controls="approvals">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Approvals</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['account/approval']) }}" id="approvals">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('account.approval.index')}}" class="nav-link {{ active_class(['account/approval']) }}">Customer</a>
            </li>
          </ul>
        </div>
      </li>

 


      <li class="nav-item {{ active_class(['users']) }}">
        <a href="{{ url('users') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Users</span>
        </a>
      </li>

      
      @endif

      {{-- cashier routes --}}
      @if (Auth::user()->role == 3)
        <li class="nav-item {{ active_class(['orders']) }}">
          <a href="{{ url('orders') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Orders</span>
          </a>
        </li> 

        <li class="nav-item {{ active_class(['meal-plan']) }} {{ active_class(['rooms']) }} {{ active_class(['reservations']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#acc" role="button" aria-expanded="{{ is_active_route(['reservations']) }} {{ is_active_route(['rooms']) }} {{ is_active_route(['meal-plan']) }}" aria-controls="acc">
            <ion-icon class="link-icon" name="person-outline"></ion-icon>
            <span class="link-title">Accommodation</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['meal-plan']) }} {{ show_class(['rooms']) }} {{ show_class(['reservations']) }}" id="acc">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{route('reservations.index')}}" class="nav-link {{ active_class(['reservations']) }}">Bookings</a>
              </li>
              <li class="nav-item">
                <a href="{{route('rooms.index')}}" class="nav-link {{ active_class(['rooms']) }}">Rooms</a>
              </li>
              <li class="nav-item">
                <a href="{{route('meal-plan.index')}}" class="nav-link {{ active_class(['meal-plan']) }}">Meal Plans</a>
              </li>
              
            </ul>
          </div>
        </li>

        <li class="nav-item {{ active_class(['accounts']) }}  {{ active_class(['accounts/*']) }}">
          <a href="{{ url('accounts') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Customers</span>
          </a>
        </li>
      @endif

      {{-- waiter routes --}}
      @if (Auth::user()->role == 4)
        <li class="nav-item {{ active_class(['select/point-of-sale']) }}">
          <a href="{{ route('select.pos') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">POS</span>
          </a>
        </li>

        <li class="nav-item {{ active_class(['waiter/orders']) }}">
          <a href="{{ route('waiter.orders') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title"> Orders</span>
          </a>
        </li>
      
      @endif

      {{-- front office --}}
      @if (Auth::user()->role == 5)
        <li class="nav-item {{ active_class(['meal-plan']) }} {{ active_class(['rooms']) }} {{ active_class(['reservations']) }}">
          <a class="nav-link" data-bs-toggle="collapse" href="#acc" role="button" aria-expanded="{{ is_active_route(['reservations']) }} {{ is_active_route(['rooms']) }} {{ is_active_route(['meal-plan']) }}" aria-controls="acc">
            <ion-icon class="link-icon" name="person-outline"></ion-icon>
            <span class="link-title">Accommodation</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse {{ show_class(['meal-plan']) }} {{ show_class(['rooms']) }} {{ show_class(['reservations']) }}" id="acc">
            <ul class="nav sub-menu">
              <li class="nav-item">
                <a href="{{route('reservations.index')}}" class="nav-link {{ active_class(['reservations']) }}">Bookings</a>
              </li>
              <li class="nav-item">
                <a href="{{route('rooms.index')}}" class="nav-link {{ active_class(['rooms']) }}">Rooms</a>
              </li>
              
              <li class="nav-item">
                <a href="{{route('packages.index')}}" class="nav-link {{ active_class(['packages']) }}">Packages</a>
              </li>
            </ul>
          </div>
        </li>


        <li class="nav-item {{ active_class(['front-office/pos']) }} ">
          <a href="{{ route('front.office.pos') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Direct Sales</span>
          </a>
        </li>


        
        <li class="nav-item {{ active_class(['front-office/order/list']) }} ">
          <a href="{{ route('front.office.orderList') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Orders</span>
          </a>
        </li>



        <li class="nav-item {{ active_class(['front-office/store']) }}">
          <a href="{{ route('front.office.index') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Store</span>
          </a>
        </li>


        <li class="nav-item {{ active_class(['office-requisition']) }}  {{ active_class(['office-requisition/*']) }}">
          <a href="{{ route('office.requisition.view') }}" class="nav-link">
            <ion-icon class="link-icon" name="home-outline" ></ion-icon>
            <span class="link-title">Material requisition</span>
          </a>
        </li>
        
      @endif
    </ul>
  </div>
</nav>

{{-- <li class="nav-item {{ active_class(['reservations']) }}">
        <a href="{{ url('reservations') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Reservations</span>
        </a>
      </li> --}}

      {{-- <li class="nav-item {{ active_class(['rooms']) }}">
        <a href="{{ url('rooms') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Rooms</span>
        </a>
      </li>

      <li class="nav-item {{ active_class(['room-types']) }}">
        <a href="{{ url('room-types') }}" class="nav-link">
          <ion-icon class="link-icon" name="home-outline" ></ion-icon>
          <span class="link-title">Room Types</span>
        </a>
      </li> --}}


           {{-- <li class="nav-item {{ active_class(['office-requisition']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#office" role="button" aria-expanded="{{ is_active_route(['office-requisition']) }}" aria-controls="office">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Office Department</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['office-requisition']) }}" id="office">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('office.requisition.view')}}" class="nav-link {{ active_class(['office-requisition']) }}">Material Requisition</a>
            </li>
           
          </ul>
        </div>
      </li> --}}


      {{-- <li class="nav-item {{ active_class(['house-requisition']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#house" role="button" aria-expanded="{{ is_active_route(['house-requisition']) }}" aria-controls="house">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Hse Keeping Dept</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['house-requisition']) }}" id="house">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('house.requisition.view')}}" class="nav-link {{ active_class(['house-requisition']) }}">Material Requisition</a>
            </li>
           
          </ul>
        </div>
      </li>


      <li class="nav-item {{ active_class(['maintenance-requisition']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#maintenance" role="button" aria-expanded="{{ is_active_route(['maintenance-requisition']) }}" aria-controls="maintenance">
          <ion-icon class="link-icon" name="person-outline"></ion-icon>
          <span class="link-title">Maintenance Dept</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['maintenance-requisition']) }}" id="maintenance">
          <ul class="nav sub-menu">
            <li class="nav-item">
              <a href="{{route('maintenance.requisition.view')}}" class="nav-link {{ active_class(['maintenance-requisition']) }}">Material Requisition</a>
            </li>
           
          </ul>
        </div>
      </li> --}}