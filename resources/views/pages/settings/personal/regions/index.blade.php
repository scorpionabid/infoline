@extends('pages.settings.personal.index')

@section('tab-content')
<div class="row">
   <div class="col-12 mb-4">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Regionlar</h5>
               <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#regionModal">
                   <i class="fas fa-plus"></i> Region əlavə et
               </button>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Region adı</th>
                               <th>Telefon</th>
                               <th>Sektor sayı</th>
                               <th>Məktəb sayı</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                           @foreach($regions as $region)
                           <tr>
                               <td>{{ $region->name }}</td>
                               <td>{{ $region->phone }}</td>
                               <td>{{ $region->sectors_count }}</td>
                               <td>{{ $region->schools_count }}</td>
                               <td>
                                   <div class="btn-group">
                                       <button class="btn btn-sm btn-outline-primary" 
                                               onclick="editRegion({{ $region->id }})">
                                           <i class="fas fa-edit"></i>
                                       </button>
                                       @if($region->sectors_count == 0)
                                       <button class="btn btn-sm btn-outline-danger" 
                                               onclick="deleteRegion({{ $region->id }})">
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
</div>
@endsection