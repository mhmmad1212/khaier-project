@extends('themes.default.layouts.app')

@section('title','الجمعية العمومية - '.$association->name)

@push('styles')
<style>

.ga-wrap{
background:linear-gradient(180deg,#f4f8f7 0%, #ffffff 260px);
padding:50px 0 80px;
}

.ga-hero{
background:linear-gradient(135deg,#127962,#0d5948);
border-radius:30px;
padding:38px;
color:#fff;
margin-bottom:30px;
}

.ga-title{
font-size:2.2rem;
font-weight:800;
}

.ga-grid{
margin-top:25px;
}

.ga-card{
background:#fff;
border-radius:22px;
border:1px solid rgba(18,121,98,.08);
box-shadow:0 16px 38px rgba(15,23,42,.08);
padding:22px;
height:100%;
transition:.25s;
}

.ga-card:hover{
transform:translateY(-5px);
box-shadow:0 22px 48px rgba(15,23,42,.12);
}

.ga-name{
font-weight:800;
font-size:1.1rem;
margin-bottom:6px;
}

.ga-meta{
color:#6b7280;
font-size:.9rem;
margin-top:6px;
}

.ga-badge{
display:inline-block;
background:#eef7f4;
color:#127962;
padding:6px 10px;
border-radius:8px;
font-weight:700;
font-size:.8rem;
margin-top:8px;
}

</style>
@endpush

@section('content')

<section class="ga-wrap">

<div class="container">

<div class="ga-hero">

<h1 class="ga-title">أعضاء الجمعية العمومية</h1>

<p>
تعرف على أعضاء الجمعية العمومية الذين يمثلون الهيئة العامة للجمعية.
</p>

</div>

<div class="row g-4 ga-grid">

@foreach($members as $member)

<div class="col-lg-3 col-md-4 col-sm-6">

<div class="ga-card">

<div class="ga-name">
{{ $member->name }}
</div>

@if(!empty($member->membership_number))
<div class="ga-meta">
رقم العضوية: {{ $member->membership_number }}
</div>
@endif

@if(!empty($member->membership_type))
<div class="ga-badge">
{{ $member->membership_type }}
</div>
@endif

@if(!empty($member->membership_date))
<div class="ga-meta">
تاريخ الانضمام: {{ $member->membership_date }}
</div>
@endif

</div>

</div>

@endforeach

</div>

</div>

</section>

@endsection
