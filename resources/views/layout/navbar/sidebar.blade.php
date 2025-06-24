<ul class="navbar-nav  bg-gradient-primary  sidebar sidebar-dark accordion" id="accordionSidebar">

     <!-- Sidebar - Brand -->
     <a class=" d-flex align-items-center justify-content-center" href="/dashboard">
         <div class="row mt-4 mb-4 mx-auto">
             {{-- <div class=" col-sm-3 d-sm-block d-none"><img class="img-fluid" src="asset/img/logo.png" alt="">
             </div> --}}
             <div class=" my-auto col-sm-12 d-none d-sm-block text-center"><img class="img-fluid w-75"
                     src="{{ asset('images/Logo_Fiks_PUI-11.png') }}" alt="Logo PUI">
             </div>

             <div class=" my-auto col-12 d-block d-sm-none text-center"><img class="img-fluid w-75"
                     src="{{ asset('images/Logo_Fiks_PUI-11.png') }}" alt="Logo PUI">
             </div>
         </div>
     </a>

     <!-- Divider -->
     <hr class="sidebar-divider my-0">

     <div class="sidebar-heading">
         Daftar menu
     </div>
     @if (Auth()->user()->roles_id == 2 || Auth()->user()->roles_id == 1)
         <!-- Nav Item - Dashboard -->
         <li class="nav-item  {{ Request::is('dashboard') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('dashboard') }}">
                 <i class="fas fa-fw fa-chart-line"></i>
                 <span>Dashboard</span></a>
         </li>
     @endif
     @if (Auth()->user()->roles_id == 3)
         <!-- Nav Item - Dashboard -->
         <li class="nav-item  {{ Request::is('home') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('home') }}">
                 <i class="fa-solid fa-house"></i>
                 <span>Home</span></a>
         </li>
     @endif

     @if (Auth()->user()->hasRole('talent_admin'))
         <!-- Nav Item - Talent Admin Dashboard -->
         <li class="nav-item  {{ Request::is('talent-admin/dashboard') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.dashboard') }}">
                 <i class="fas fa-fw fa-tachometer-alt"></i>
                 <span>Beranda</span></a>
         </li>

         <!-- Nav Item - Analytics (Phase 1 Enhancement) -->
         <li class="nav-item  {{ Request::is('talent-admin/analytics') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.analytics') }}">
                 <i class="fas fa-fw fa-chart-bar"></i>
                 <span>Analitik</span></a>
         </li>

         <!-- Nav Item - Manage Requests -->
         <li class="nav-item  {{ Request::is('talent-admin/manage-requests*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.manage_requests') }}">
                 <i class="fas fa-fw fa-handshake"></i>
                 <span>Permintaan Talent</span></a>
         </li>

         <!-- PROJECT-CENTRIC SYSTEM -->
         <!-- Nav Item - Project Management -->
         <li class="nav-item  {{ Request::is('admin/projects*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('admin.projects.index') }}">
                 <i class="fas fa-fw fa-cogs"></i>
                 <span>Permintaan Proyek</span></a>
         </li>

         <!-- Nav Item - Manage Talents -->
         <li class="nav-item  {{ Request::is('talent-admin/manage-talents') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.manage_talents') }}">
                 <i class="fas fa-fw fa-user-tie"></i>
                 <span>Manajemen Talent</span></a>
         </li>

         <!-- Nav Item - Manage Recruiters -->
         <li class="nav-item  {{ Request::is('talent-admin/manage-recruiters') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.manage_recruiters') }}">
                 <i class="fas fa-fw fa-briefcase"></i>
                 <span>Manajemen Rekruter</span></a>
         </li>

         <!-- Nav Item - Manage Talent Admins -->
         <li class="nav-item  {{ Request::is('talent-admin/manage-talent-admins*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent_admin.manage_talent_admins') }}">
                 <i class="fas fa-fw fa-user-shield"></i>
                 <span>Manajemen Admin</span></a>
         </li>
     @endif

     @if (Auth()->user()->hasRole('talent'))
         <!-- Nav Item - Talent Dashboard -->
         <li class="nav-item  {{ Request::is('talent/dashboard') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent.dashboard') }}">
                 <i class="fas fa-fw fa-user-tie"></i>
                 <span>Beranda</span></a>
         </li>

         <!-- Nav Item - My Requests (Legacy System) -->
         <li class="nav-item  {{ Request::is('talent/my-requests*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent.my_requests') }}">
                 <i class="fas fa-fw fa-tasks"></i>
                 <span>Permintaan Saya</span></a>
         </li>

         <!-- PROJECT-CENTRIC SYSTEM -->
         <!-- Nav Item - My Assignments -->
         <li class="nav-item  {{ Request::is('talent/assignments*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('talent.assignments.index') }}">
                 <i class="fas fa-fw fa-briefcase"></i>
                 <span>Penugasan Saya</span></a>
         </li>
     @endif

     @if (Auth()->user()->hasRole('recruiter'))
         <!-- Nav Item - Recruiter Dashboard -->
         <li class="nav-item  {{ Request::is('recruiter/dashboard') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('recruiter.dashboard') }}">
                 <i class="fas fa-fw fa-search"></i>
                 <span>Beranda</span></a>
         </li>

         <!-- Nav Item - My Requests (Legacy System) -->
         <li class="nav-item  {{ Request::is('recruiter/my-requests*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('recruiter.my_requests') }}">
                 <i class="fas fa-fw fa-clipboard-list"></i>
                 <span>Permintaan Saya</span></a>
         </li>

         <!-- PROJECT-CENTRIC SYSTEM -->
         <!-- Nav Item - My Projects -->
         <li class="nav-item  {{ Request::is('projects') || Request::is('projects/index') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('projects.index') }}">
                 <i class="fas fa-fw fa-project-diagram"></i>
                 <span>Proyek Saya</span></a>
         </li>

         <!-- Nav Item - Create Project -->
         <li class="nav-item  {{ Request::is('projects/create') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('projects.create') }}">
                 <i class="fas fa-fw fa-plus-circle"></i>
                 <span>Buat Proyek</span></a>
         </li>
     @endif


     <!-- Divider -->
     <hr class="sidebar-divider">

     {{-- ADMIN --}}
     @if (Auth()->User()->roles_id == 1)
         <!-- Nav Item - Pages Collapse Menu -->

         <li class="nav-item {{ Request::is('data-pengajar*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('viewPengajar') }}"> <i class="fa-solid fa-chalkboard-user"></i>
                 <span>Data Pengajar</span></a>
         </li>

         <li class="nav-item {{ Request::is('data-mapel*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('viewMapel') }}"><i class="fa-solid fa-book"></i>
                 <span>Mata Pelajaran</span></a>
         </li>

         <li class="nav-item {{ Request::is('data-kelas*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('viewKelas') }}"><i class="fa-solid fa-school-flag"></i>
                 <span>Kelas</span></a>
         </li>

         <li class="nav-item {{ Request::is('data-siswa*') ? 'active' : '' }} animate-btn">
             <a class="nav-link" href="{{ route('viewSiswa') }}"><i class="fa-solid fa-users"></i>
                 <span>Data Siswa</span></a>
         </li>

         <!-- Divider -->
         <hr class="sidebar-divider">
     @endif

     {{-- PENGAJAR --}}
     @if (Auth()->User()->roles_id == 2)
         @foreach ($assignedKelas as $assignedKelasItem)
             <!-- Nav Item - Pages Collapse Menu -->
             <li
                 class="nav-item {{ explode('/', Request::segment(2))[0] == $assignedKelasItem['mapel']->id ? 'active' : '' }} ">
                 <a class="nav-link collapsed" href="#" data-toggle="collapse"
                     data-target="#collapseTarget{{ $loop->iteration }}" aria-expanded="true"
                     aria-controls="collapseTarget">
                     <i class="fa-solid fa-book-bookmark"></i>
                     <span>{{ $assignedKelasItem['mapel']->name }}</span>
                 </a>
                 <div id="collapseTarget{{ $loop->iteration }}" class="collapse" aria-labelledby="headingTwo"
                     data-parent="#accordionSidebar">
                     <div class="bg-white py-2 collapse-inner rounded">
                         <h6 class="collapse-header fw-bold">Kelas :</h6>
                         @if ($assignedKelasItem['kelas'])
                             @foreach ($assignedKelasItem['kelas'] as $kelas)
                                 <a href="{{ route('viewKelasMapel', ['mapel' => $assignedKelasItem['mapel']->id, 'token' => encrypt($kelas->id), 'mapel_id' => $assignedKelasItem['mapel']]) }}"
                                     class="collapse-item">
                                     {{ $kelas->name }}
                                 </a>
                             @endforeach
                         @else
                             <span class="small">(No Class)</span>
                         @endif
                     </div>
                 </div>
             </li>
         @endforeach
     @endif

     {{-- SISWA --}}

     @unless (Request::routeIs('userUjian'))
         <!-- Konten yang akan ditampilkan jika permintaan tidak sesuai dengan rute 'userUjian' -->
         @if (Auth()->user()->roles_id == 3)
             <!-- Nav Item - Pages Collapse Menu -->
             @foreach ($assignedKelas as $assignedKelasItem)
                 <!-- Nav Item - Pages Collapse Menu -->
                 <li
                     class="nav-item {{ Request::segment(2) == 'learning' && Request::segment(3) == $assignedKelasItem['mapel']->id ? 'active' : '' }} ">
                     <a class="nav-link collapsed animate-btn"
                         href="{{ route('viewKelasMapel', ['mapel' => $assignedKelasItem['mapel']->id, 'token' => encrypt($assignedKelasItem['mapel']->id), 'mapel_id' => $assignedKelasItem['mapel']->id]) }}">
                         <i class="fa-solid fa-book-bookmark"></i>
                         <span>{{ $assignedKelasItem['mapel']->name }}</span>
                     </a>
                 </li>
             @endforeach
         @endif
     @endunless


     <!-- Nav Item - Utilities Collapse Menu -->

     <!-- Divider -->
     {{-- <hr class="sidebar-divider"> --}}


     <!-- Divider -->
     <hr class="sidebar-divider d-none d-md-block">

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
     </div>

 </ul>
