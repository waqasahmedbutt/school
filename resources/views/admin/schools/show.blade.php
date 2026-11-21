@extends('layouts.app')

@section('title', $school->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $school->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.schools.edit', $school) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('admin.schools.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </div>

    <!-- School Details -->
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">School Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-gray-600">School Code</p>
                <p class="font-semibold">{{ $school->code ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600">Phone Number</p>
                <p class="font-semibold">{{ $school->phone ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-600">Address</p>
                <p class="font-semibold">{{ $school->address ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Classrooms -->
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Classrooms ({{ $school->classrooms->count() }})</h2>
            <a href="{{ route('admin.classrooms.create') }}?school_id={{ $school->id }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                Add Classroom
            </a>
        </div>
        
        @if($school->classrooms->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Classroom
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Sections
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Students
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($school->classrooms as $classroom)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap font-semibold">
                                {{ $classroom->name }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ Str::limit($classroom->description, 50) }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $classroom->sections->count() }} sections
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $classroom->students->count() }} students
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.classrooms.show', $classroom) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                    View
                                </a>
                                <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="text-green-600 hover:text-green-900 text-sm">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500">No classrooms found for this school.</p>
        @endif
    </div>
</div>
@endsection