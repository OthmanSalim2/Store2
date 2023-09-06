@extends('layouts.dashboard')

@section('title', 'Categories')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')

    <div class="mb-3">
        <a href="{{ route('dashboard.categories.create') }}" class="btn btn-sm btn-outline-primary mr-2">Create</a>
        <a href="{{ route('dashboard.categories.trash') }}" class="btn btn-sm btn-outline-secondary">Trash</a>
    </div>

    <div>
        <x-alert type="success" />
        <x-alert type="info" />
    </div>

    <form action="{{ URL::current() }}" method="get" class="d-flex justify-content-between mb-4">
        <x-form.input name="name" placeholder="Name" class="mx-2" :value="request('name')" />

        <select name="status" class="form-control mx-2">
            <option value="">All</option>
            <option value="active" @selected(request('status') == 'active')>Active</option>
            <option value="archived" @selected(request('status') == 'archived')>Archived</option>
        </select>
        <button class="btn btn-dark mx-2">Filter</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Parent Name</th>
                <th>Active Products Count</th>
                <th>Status</th>
                <th>Create At</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td><img style="height:50px; width: 70px; border-radius:6px;"
                            src="{{ asset('storage/' . $category->image) }}" alt="#"></td>
                    <td>{{ $category->id }}</td>
                    <td>
                        <a href="{{ route('dashboard.categories.show', $category) }}">
                            {{ $category->name }}
                        </a>
                    </td>
                    {{-- <td>{{ $category->parent_name }}</td> --}}
                    <td>{{ $category->parent->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>{{ $category->status }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>
                        <a href="{{ route('dashboard.categories.edit', $category->id) }}"
                            class="btn btn-sm btn-outline-success">Edit</a>
                    </td>
                    <td>
                        {{-- the route of delete not direct link mean must sent by delete method  --}}
                        <form action="{{ route('dashboard.categories.destroy', ['category' => $category]) }}"
                            method="post">
                            @csrf
                            {{-- Form Method Spoofing --}}
                            {{-- here I say to server work with this reponse as delete method --}}
                            @method('delete')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="table-danger">The Cateories are empty !!</td>
                </tr>
            @endforelse

        </tbody>
    </table>

    {{-- withQueryString() this methos it use if I pass the parameters in url --}}

    {{ $categories->withQueryString()->links() }}
    {{-- here use appends() if I need pass the parameter in url --}}
    {{-- {{ $categories->withQueryString()->appends(['search' => 1])->links() }} --}}

@endsection
