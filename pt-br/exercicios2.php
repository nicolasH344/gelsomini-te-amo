<!-- ... existing code ... -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Incluindo Font Awesome para os ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS para a página do exercício -->
    <style>
        /* ===== VARIÁVEIS CSS PARA TEMAS ===== */
        /* Tema Roxo Moderno (Padrão) */
        :root,
        .theme-purple {
            /* Cores principais */
            --primary-color: #6f42c1;
            --primary-dark: #5a2d91;
            --primary-light: #8e5dd9;
            --secondary-color: #e83e8c;
            --accent-color: #fd7e14;
            
            /* Gradientes */
            --gradient-primary: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 50%, #e83e8c 100%);
            --gradient-secondary: linear-gradient(45deg, #6f42c1, #e83e8c);
            --gradient-hero: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 25%, #e83e8c 50%, #fd7e14 75%, #6f42c1 100%);
            
            /* Cores de fundo */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-dark: #212529;
            --bg-light: #ffffff;
            
            /* Cores de texto */
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-light: #ffffff;
            --text-muted: #6c757d;
            
            /* Cores de estado */
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            
            /* Sombras */
            --shadow-sm: 0 0.125rem 0.25rem rgba(111, 66, 193, 0.075);
            --shadow: 0 0.5rem 1rem rgba(111, 66, 193, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(111, 66, 193, 0.175);
            
            /* Bordas */
            --border-radius: 0.375rem;
            --border-radius-lg: 0.5rem;
            --border-color: #dee2e6;
            
            /* Animações */
            --transition: all 0.3s ease;
            --animation-duration: 0.3s;
        }

        /* Tema Escuro */
        .theme-dark {
            --primary-color: #bb86fc;
            --primary-dark: #9c4dcc;
            --primary-light: #d1b3ff;
            --secondary-color: #03dac6;
            --accent-color: #cf6679;
            
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-dark: #000000;
            --bg-light: #2d2d2d;
            
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --text-light: #ffffff;
            --text-muted: #888888;
            
            --border-color: #333333;
            
            --gradient-primary: linear-gradient(135deg, #bb86fc 0%, #d1b3ff 50%, #03dac6 100%);
            --gradient-secondary: linear-gradient(45deg, #bb86fc, #03dac6);
            --gradient-hero: linear-gradient(135deg, #bb86fc 0%, #d1b3ff 25%, #03dac6 50%, #cf6679 75%, #bb86fc 100%);
        }

        /* ===== ESTILOS BASE ===== */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            transition: var(--transition);
        }

        /* ===== BOTÕES ===== */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: var(--text-light);
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--text-light);
        }
        
        .btn-secondary {
             background: var(--secondary-color);
             color: var(--text-light);
        }
        
        /* ===== BADGES ===== */
        .badge {
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            font-size: .75em;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            margin-right: 5px;
        }

        /* ===== ESTILOS ESPECÍFICOS DO EXERCÍCIO ===== */
        .container {
            max-width: 900px;
            margin: auto;
            background: var(--bg-primary);
            padding: 25px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
        }

        .exercise-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .exercise-header h1 {
            font-size: 2rem;
            margin: 0;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }
        
        /* Cores dos badges para compatibilidade */
        .badge-html { background-color: var(--danger-color); }
        .badge-css { background-color: #007bff; }
        .badge-js { background-color: var(--warning-color); color: var(--text-primary); }
        .badge-iniciante { background-color: var(--success-color); }
        .badge-intermediario { background-color: var(--warning-color); color: var(--text-primary); }

        .exercise-content h2 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .code-editor {
            width: 100%;
            min-height: 250px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 1rem;
            resize: vertical;
            background-color: #282c34;
            color: #abb2bf;
        }
        
        .theme-dark .code-editor {
             background-color: #1e1e1e;
             border-color: var(--border-color);
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .instructions {
            background-color: color-mix(in srgb, var(--primary-color) 10%, var(--bg-secondary));
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>
