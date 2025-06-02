@extends('layouts.app')

@section('title', 'Statystyki')

@section('header', 'Statystyki')

{{-- Navigation is handled in the main layout --}}

@section('content')
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Użytkownicy</h3>
            <div class="stat-number">{{ $users }}</div>
            <a href="#uzytkownicy" class="stat-link">Zarządzaj użytkownikami</a>
        </div>
        <div class="stat-card">
            <h3>Przewoźnicy</h3>
            <div class="stat-number">{{ $carriers }}</div>
            <a href="#przewoznicy" class="stat-link">Zarządzaj przewoźnikami</a>
        </div>
        <div class="stat-card">
            <h3>Linie</h3>
            <div class="stat-number">{{ $lines }}</div>
            <a href="#miedzymiastowe" class="stat-link">Zarządzaj liniami</a>
        </div>
    </div>
    
    <div class="admin-actions">
        <h2>Szybkie akcje</h2>
        <div class="action-buttons">
            <a href="{{ route('admin') }}" class="action-button">Panel administratora</a>
            <button class="action-button" id="backup-button">Wykonaj kopię bazy danych</button>
            <button class="action-button" id="logs-button">Przeglądaj logi</button>
        </div>
    </div>
    
    <div class="recent-activity">
        <h2>Ostatnie działania</h2>
        <table class="activity-table">
            <tr>
                <th>Data</th>
                <th>Użytkownik</th>
                <th>Działanie</th>
            </tr>
            <tr>
                <td>{{ now()->format('Y-m-d H:i') }}</td>
                <td>{{ Auth::user()->name }}</td>
                <td>Logowanie do panelu</td>
            </tr>
        </table>
    </div>
@endsection

@section('styles')
<style>
    .dashboard-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .stat-card {
        flex: 1;
        padding: 20px;
        background-color: #f5f5f5;
        border-radius: 8px;
        margin: 0 10px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-number {
        font-size: 48px;
        font-weight: bold;
        margin: 10px 0;
        color: #333;
    }
    
    .stat-link {
        display: block;
        margin-top: 10px;
    }
    
    .admin-actions {
        margin: 30px 0;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
    }
    
    .action-button {
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .action-button:hover {
        background-color: #0056b3;
    }
    
    .recent-activity {
        margin-top: 30px;
    }
    
    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .activity-table th, .activity-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    
    .activity-table th {
        background-color: #f2f2f2;
    }
</style>
@endsection
