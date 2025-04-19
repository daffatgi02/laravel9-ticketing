@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Department Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="fas fa-plus-circle me-1"></i> Add Department
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ url('/admin/departments') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search departments..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th width="15%">Users Count</th>
                            <th width="10%">Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments ?? [] as $department)
                        <tr>
                            <td>{{ $department->id }}</td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->code }}</td>
                            <td>{{ $department->users_count ?? 0 }}</td>
                            <td>
                                @if($department->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary edit-department"
                                        data-id="{{ $department->id }}"
                                        data-name="{{ $department->name }}"
                                        data-code="{{ $department->code }}"
                                        data-active="{{ $department->active }}"
                                        data-bs-toggle="modal" data-bs-target="#editDepartmentModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-department"
                                        data-id="{{ $department->id }}"
                                        data-name="{{ $department->name }}"
                                        data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">No departments found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($departments) && $departments->hasPages())
        <div class="card-footer">
            {{ $departments->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Department Code</label>
                        <input type="text" class="form-control" id="code" name="code">
                        <small class="text-muted">Optional short code for the department (e.g., IT, HR)</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDepartmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Form contents remain the same -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Department Modal -->
<div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the department: <strong id="delete_department_name"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteDepartmentForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Department
        document.querySelectorAll('.edit-department').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const active = this.getAttribute('data-active');

                document.getElementById('edit_name').value = name;
                document.getElementById('edit_code').value = code;
                document.getElementById('edit_active').checked = active === '1';

                document.getElementById('editDepartmentForm').action = "{{ route('admin.departments.update', '') }}/" + id;
            });
        });

        // Delete Department
        document.querySelectorAll('.delete-department').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('delete_department_name').textContent = name;
                document.getElementById('deleteDepartmentForm').action = "{{ route('admin.departments.destroy', '') }}/" + id;
            });
        });
    });
</script>
@endsection
