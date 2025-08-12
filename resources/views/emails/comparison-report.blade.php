{{-- filepath: c:\laragon\www\EBR\resources\views\emails\comparison-report.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comparativa Educativa</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background-color: #4F46E5; color: white; padding: 20px; border-radius: 8px 8px 0 0; margin: -30px -30px 30px -30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .stats { display: flex; justify-content: space-between; margin: 20px 0; }
        .stat { text-align: center; padding: 15px; background-color: #f8f9fa; border-radius: 6px; }
        .stat-number { font-size: 24px; font-weight: bold; color: #4F46E5; }
        .stat-label { font-size: 12px; color: #6b7280; margin-top: 5px; }
        .button { display: inline-block; background-color: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Comparativa Educativa</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">{{ $comparison->name }}</p>
        </div>
        
        <p>Hola,</p>
        
        <p>{{ $sender_name }} te ha compartido una comparativa educativa generada en el sistema EBR.</p>
        
        @if($message)
            <div style="background-color: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 6px 6px 0;">
                <p style="margin: 0; font-style: italic;">"{{ $message }}"</p>
            </div>
        @endif
        
        <div class="stats">
            <div class="stat">
                <div class="stat-number">{{ $comparison->files_count }}</div>
                <div class="stat-label">Archivos Comparados</div>
            </div>
            <div class="stat">
                <div class="stat-number">{{ number_format($comparison->total_institutions) }}</div>
                <div class="stat-label">Instituciones</div>
            </div>
            <div class="stat">
                <div class="stat-number">{{ number_format($comparison->total_students) }}</div>
                <div class="stat-label">Estudiantes</div>
            </div>
        </div>
        
        <h3>🎯 Tipos de Análisis Incluidos:</h3>
        <ul>
            @foreach($comparison->formatted_chart_types as $chartType)
                <li>{{ $chartType }}</li>
            @endforeach
        </ul>
        
        <p>Puedes acceder a la comparativa interactiva completa en:</p>
        <a href="{{ $comparison->share_url }}" class="button">Ver Comparativa Interactiva</a>
        
        @if($pdf_path || $excel_path)
            <h3>📎 Archivos Adjuntos:</h3>
            <ul>
                @if($pdf_path)
                    <li><strong>Reporte en PDF:</strong> Resumen visual de la comparativa</li>
                @endif
                @if($excel_path)
                    <li><strong>Dataset Excel:</strong> Datos completos para análisis adicional</li>
                @endif
            </ul>
        @endif
        
        <div class="footer">
            <p>Este correo fue enviado desde el Sistema de Gestión EBR.</p>
            <p>Fecha de generación: {{ $comparison->created_at->format('d/m/Y H:i:s') }}</p>
            <p>Si tienes problemas para acceder al enlace, copia y pega esta URL en tu navegador:<br>
            {{ $comparison->share_url }}</p>
        </div>
    </div>
</body>
</html>