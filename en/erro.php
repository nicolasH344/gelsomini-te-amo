<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Erro 404 - Página não encontrada</title>
    <style>
        /* Inclua aqui o CSS fornecido pelo usuário */
        /* Só para demonstração, copiado resumido e adaptado direto do código fornecido */

        :root {
            --primary-color: #6f42c1;
            --primary-dark: #5a2d91;
            --primary-light: #8e5dd9;
            --secondary-color: #e83e8c;
            --accent-color: #fd7e14;

            --gradient-primary: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 50%, #e83e8c 100%);
            --gradient-hero: linear-gradient(135deg, #6f42c1 0%, #8e5dd9 25%, #e83e8c 50%, #fd7e14 75%, #6f42c1 100%);

            --bg-primary: #ffffff;
            --text-primary: #212529;
            --text-light: #ffffff;
            --shadow-lg: 0 1rem 3rem rgba(111, 66, 193, 0.175);
            --border-radius: 0.375rem;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: var(--transition);
            background: var(--gradient-hero);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            text-align: center;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .error-container {
            background: var(--bg-primary);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            max-width: 600px;
            width: 90%;
        }

        h1 {
            font-size: 4rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        p {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }

        a.btn-primary {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1.25rem;
            color: var(--text-light);
            background: var(--gradient-primary);
            border-radius: var(--border-radius);
            text-decoration: none;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        a.btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 1rem 3rem rgba(111, 66, 193, 0.3);
            color: var(--text-light);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p>Desculpe, a página que você está procurando não foi encontrada.</p>
        <a href="index.php" class="btn-primary" role="button">Voltar para a Página Inicial</a>
    </div>
</body>
</html>
