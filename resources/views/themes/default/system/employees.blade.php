@extends('themes.default.layouts.app')

@section('title','الموظفون - '.$association->name)

@push('styles')
<style>

.emp-wrap{
background:linear-gradient(180deg,#f4f8f7 0%, #ffffff 260px);
padding:50px 0 80px;
}

.emp-hero{
background:linear-gradient(135deg,#127962,#0d5948);
border-radius:30px;
padding:38px;
color:#fff;
margin-bottom:30px;
}

.emp-title{
font-size:2.2rem;
font-weight:800;
}

.emp-card{
background:#fff;
border-radius:24px;
border:1px solid rgba(18,121,98,.08);
box-shadow:0 18px 40px rgba(15,23,42,.08);
padding:24px;
text-align:center;
height:100%;
transition:.25s;
}

.emp-card:hover{
transform:translateY(-6px);
box-shadow:0 22px 50px rgba(15,23,42,.12);
}

.emp-photo{
width:110px;
height:110px;
border-radius:50%;
margin:auto;
margin-bottom:14px;
overflow:hidden;
background:#eef2f7;
display:flex;
align-items:center;
justify-content:center;
font-size:40px;
color:#127962;
}

.emp-photo img{
width:100%;
height:100%;
object-fit:cover;
}

.emp-name{
font-weight:800;
font-size:1.15rem;
margin-bottom:4px;
}

.emp-position{
color:#127962;
font-weight:700;
font-size:.95rem;
margin-bottom:10px;
}

.emp-meta{
font-size:.9rem;
color:#6b7280;
margin-top:4px;
}

</style>
@endpush

@section('content')

<section class="emp-wrap">

<div class="container">

<div class="emp-hero">

<h1 class="emp-title">فريق العمل</h1>

<p>
تعرف على فريق العمل في الجمعية والكوادر التي تسهم في تنفيذ البرامج والمبادرات.
</p>

</div>

<div class="row g-4">

@foreach($employees as $emp)

<div class="col-lg-3 col-md-4 col-sm-6">

<div class="emp-card">

<div class="emp-photo">

@if(!empty($emp->photo))
<img loading="lazy" decoding="async" src="{{ $emp->photo }}">
@else
<i class="bi bi-person"></i>
@endif

</div>

<div class="emp-name">
{{ $emp->name }}
</div>

@if(!empty($emp->position))
<div class="emp-position">
{{ $emp->position }}
</div>
@endif

@if(!empty($emp->department))
<div class="emp-meta">
القسم: {{ $emp->department }}
</div>
@endif

@if(!empty($emp->phone))
<div class="emp-meta">
{{ $emp->phone }}
</div>
@endif

@if(!empty($emp->email))
<div class="emp-meta">
{{ $emp->email }}
</div>
@endif

</div>

</div>

@endforeach

</div>

</div>

</section>

@endsection
