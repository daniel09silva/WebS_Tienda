@extends('layouts/contentNavbarLayout')

@section('title', 'Permisos y Roles - Seguridad')

@section('page-script')
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalId = 'modalCrearRol';
        @if (old('role_id'))
            modalId = 'modalEditarRol{{ old('role_id') }}';
        @endif
        var modalEl = document.getElementById(modalId);
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    });
</script>
@endif
@endsection

@section('content')
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Roles</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearRol">
            <i class="icon-base bx bx-plus me-1"></i> Nuevo rol
        </button>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Permisos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditarRol{{ $role->id }}"><i class="icon-base bx bx-edit-alt me-1"></i> Editar</a>
                                @if ($role->name !== 'Super Admin')
                                <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEliminarRol{{ $role->id }}"><i class="icon-base bx bx-trash me-1"></i> Eliminar</a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">No hay roles registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade" id="modalCrearRol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" action="{{ route('seguridad-permisos-roles.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nuevo rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label" for="role-name">Nombre del rol</label>
                    <input type="text" id="role-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('role_id') ? '' : old('name') }}" />
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <label class="form-label fw-bold">Permisos</label>
                @foreach ($modulos as $modulo)
                    <div class="mb-3">
                        <div class="fw-medium mb-1">{{ $modulo->name }}</div>
                        <div class="row">
                            @forelse ($modulo->permissions as $permiso)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permiso->id }}" id="perm-create-{{ $permiso->id }}" />
                                    <label class="form-check-label" for="perm-create-{{ $permiso->id }}">{{ str($permiso->name)->after($modulo->slug . '.') }}</label>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-muted">Sin permisos en este módulo.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@foreach ($roles as $role)
<!-- Modal Editar Rol -->
<div class="modal fade" id="modalEditarRol{{ $role->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" action="{{ route('seguridad-permisos-roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="role_id" value="{{ $role->id }}" />
            <div class="modal-header">
                <h5 class="modal-title">Editar rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $isThisRole = (string) old('role_id') === (string) $role->id;
                    $rolePermissionIds = $role->permissions->pluck('id')->all();
                @endphp
                <div class="mb-4">
                    <label class="form-label" for="role-name-{{ $role->id }}">Nombre del rol</label>
                    <input type="text" id="role-name-{{ $role->id }}" name="name" class="form-control {{ $isThisRole && $errors->has('name') ? 'is-invalid' : '' }}" value="{{ $isThisRole ? old('name') : $role->name }}" />
                    @if ($isThisRole && $errors->has('name'))
                        <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                    @endif
                </div>
                <label class="form-label fw-bold">Permisos</label>
                @foreach ($modulos as $modulo)
                    <div class="mb-3">
                        <div class="fw-medium mb-1">{{ $modulo->name }}</div>
                        <div class="row">
                            @forelse ($modulo->permissions as $permiso)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permiso->id }}" id="perm-{{ $role->id }}-{{ $permiso->id }}" {{ in_array($permiso->id, $rolePermissionIds) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="perm-{{ $role->id }}-{{ $permiso->id }}">{{ str($permiso->name)->after($modulo->slug . '.') }}</label>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-muted">Sin permisos en este módulo.</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

@if ($role->name !== 'Super Admin')
<!-- Modal Eliminar Rol -->
<div class="modal fade" id="modalEliminarRol{{ $role->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('seguridad-permisos-roles.destroy', $role) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Eliminar rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar el rol <strong>{{ $role->name }}</strong>? Los empleados que lo tengan asignado lo perderán.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach
@endsection
