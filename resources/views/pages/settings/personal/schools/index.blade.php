@extends('pages.settings.personal.index')

@section('tab-content')
{{-- Excel Import Info --}}
<div class="alert alert-info mb-4">
   <h5><i class="fas fa-info-circle"></i> Excel ilə import qaydaları:</h5>
   <ul class="mb-0">
       <li>Şablonu yükləyin və düzgün doldurun</li>
       <li>Bütün məcburi xanaları doldurun (Məktəb adı, Sektor, UTIS kod)</li>
       <li>Şablonda olan formatı pozmayın</li>
       <li>Təkrarlanan məlumatlar qəbul edilmir</li>
   </ul>
</div>

<div class="row mb-4">
   <div class="col-md-6">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Məktəblər</h5>
               <div>
                   <a href="{{ route('settings.personal.schools.template') }}" class="btn btn-sm btn-outline-primary">
                       <i class="fas fa-download"></i> Excel şablonu
                   </a>
                   <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                       <i class="fas fa-upload"></i> Excel import
                   </button>
                   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal">
                       <i class="fas fa-plus"></i> Məktəb əlavə et
                   </button>
               </div>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>UTIS kod</th>
                               <th>Məktəb adı</th>
                               <th>Sektor</th>
                               <th>Telefon</th>
                               <th>Email</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($schools as $school)
                           <tr>
                               <td>{{ $school->utis_code }}</td>
                               <td>{{ $school->name }}</td>
                               <td>{{ $school->sector->name }}</td>
                               <td>{{ $school->phone }}</td>
                               <td>{{ $school->email }}</td>
                               <td>
                                   <div class="btn-group">
                                       <button class="btn btn-sm btn-outline-primary" onclick="editSchool({{ $school->id }})">
                                           <i class="fas fa-edit"></i>
                                       </button>
                                       @if(!$school->hasAdmins())
                                       <button class="btn btn-sm btn-outline-danger" onclick="deleteSchool({{ $school->id }})">
                                           <i class="fas fa-trash"></i>
                                       </button>
                                       @endif
                                   </div>
                               </td>
                           </tr>
                           @endforeach
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>

   <div class="col-md-6">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Məktəb Adminləri</h5>
               <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#schoolAdminModal">
                   <i class="fas fa-user-plus"></i> Admin əlavə et
               </button>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Ad Soyad</th>
                               <th>Email</th>
                               <th>UTIS kod</th>
                               <th>Məktəb</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($schoolAdmins as $admin)
                           <tr>
                               <td>{{ $admin->name }}</td>
                               <td>{{ $admin->email }}</td>
                               <td>{{ $admin->utis_code }}</td>
                               <td>{{ $admin->school->name }}</td>
                               <td>
                                   <button class="btn btn-sm btn-outline-primary" onclick="editAdmin({{ $admin->id }})">
                                       <i class="fas fa-edit"></i>
                                   </button>
                               </td>
                           </tr>
                           @endforeach
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>
@endsection