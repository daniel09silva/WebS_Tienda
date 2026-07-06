@extends('layouts/contentNavbarLayout')

@section('title', 'Modulos - Core')

@section('page-script')
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalId = 'modalCrearModulo';
        @if (old('permiso_module_id'))
            modalId = 'modalAgregarPermiso{{ old('permiso_module_id') }}';
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
        <h5 class="mb-0">Módulos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearModulo">
            <i class="icon-base bx bx-plus me-1"></i> Nuevo módulo
        </button>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Permisos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($modulos as $modulo)
                <tr>
                    <td>{{ $modulo->name }}</td>
                    <td><code>{{ $modulo->slug }}</code></td>
                    <td>
                        @forelse ($modulo->permissions as $permiso)
                            <span class="badge bg-label-primary me-1">{{ str($permiso->name)->after($modulo->slug . '.') }}</span>
                        @empty
                            <span class="text-muted">Sin permisos</span>
                        @endforelse
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarPermiso{{ $modulo->id }}">
                            <i class="icon-base bx bx-plus me-1"></i> Agregar permiso
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No hay módulos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Modulo -->
<div class="modal fade" id="modalCrearModulo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('core-modulos.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Nuevo módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label" for="mod-name">Nombre</label>
                    <input type="text" id="mod-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" />
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label" for="mod-description">Descripción (opcional)</label>
                    <input type="text" id="mod-description" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" />
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@foreach ($modulos as $modulo)
<!-- Modal Agregar Permiso -->
<div class="modal fade" id="modalAgregarPermiso{{ $modulo->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('core-modulos.permisos.store', $modulo) }}" method="POST">
            @csrf
            <input type="hidden" name="permiso_module_id" value="{{ $modulo->id }}" />
            <div class="modal-header">
                <h5 class="modal-title">Agregar permiso a {{ $modulo->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $isThisModule = (string) old('permiso_module_id') === (string) $modulo->id;
                @endphp
                <p class="text-muted">El permiso se guardará como <code>{{ $modulo->slug }}.&lt;accion&gt;</code>.</p>
                <div class="mb-2">
                    <label class="form-label" for="action-{{ $modulo->id }}">Acción (ej: ver, crear, exportar)</label>
                    <input type="text" id="action-{{ $modulo->id }}" name="action" class="form-control {{ $isThisModule && $errors->has('action') ? 'is-invalid' : '' }}" value="{{ $isThisModule ? old('action') : '' }}" />
                    @if ($isThisModule && $errors->has('action'))
                        <div class="invalid-feedback d-block">{{ $errors->first('action') }}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
