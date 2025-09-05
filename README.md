# Vitexa V1 - AI Fitness & Health Platform

![Vitexa Logo](https://via.placeholder.com/200x80/6366f1/ffffff?text=VITEXA)

**Vitexa** é uma plataforma web completa de fitness e saúde que utiliza Inteligência Artificial para criar planos personalizados de treino e dieta, oferecendo uma experiência única e motivadora para usuários que buscam melhorar sua qualidade de vida.

## 🚀 Características Principais

### 🤖 **Inteligência Artificial Integrada**
- **Geração de Planos de Treino**: Exercícios personalizados baseados no perfil do usuário
- **Planos de Dieta Balanceados**: Cardápios nutricionais com cálculo de macronutrientes
- **Chatbot Especializado**: Assistente virtual para dúvidas sobre fitness e nutrição
- **Respostas Contextualizadas**: IA que conhece o histórico e objetivos do usuário

### 📊 **Dashboard Interativo**
- **Estatísticas em Tempo Real**: IMC, peso, dias no app, progresso
- **Gráficos de Evolução**: Visualização do progresso com Chart.js
- **Treino do Dia**: Exercícios detalhados com instruções
- **Plano Nutricional**: Refeições com valores calóricos e macronutrientes

### 💪 **Gestão de Treinos**
- **Planos Semanais**: 5 dias de treino com exercícios variados
- **Instruções Detalhadas**: Como executar cada exercício corretamente
- **Progressão Personalizada**: Séries, repetições e cargas adaptadas
- **Grupos Musculares**: Organização por músculos trabalhados

### 🥗 **Nutrição Inteligente**
- **6 Refeições Diárias**: Café da manhã, lanches, almoço, jantar e ceia
- **Cálculo Nutricional**: Calorias, proteínas, carboidratos e gorduras
- **Ingredientes Brasileiros**: Alimentos acessíveis e regionais
- **Modo de Preparo**: Instruções detalhadas para cada refeição

### 📈 **Acompanhamento de Progresso**
- **Registro de Medidas**: Peso, percentual de gordura, massa muscular
- **Histórico Completo**: Evolução ao longo do tempo
- **Gráficos Interativos**: Visualização clara do progresso
- **Metas e Objetivos**: Acompanhamento de conquistas

### ⏰ **Sistema de Lembretes**
- **Lembretes Personalizados**: Treino, dieta, hidratação, medicamentos
- **Horários Flexíveis**: Configure para sua rotina
- **Dias da Semana**: Escolha os dias específicos
- **Notificações**: Email e log de atividades

### 💬 **Chat com IA**
- **Interface WhatsApp**: Conversa natural e intuitiva
- **Especialista Virtual**: Conhecimento em fitness e nutrição
- **Histórico Persistente**: Conversas salvas no banco de dados
- **Respostas Motivacionais**: Incentivo personalizado

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **PHP 8+**: Linguagem principal do servidor
- **MySQL**: Banco de dados relacional
- **Arquitetura MVC**: Organização clara e escalável
- **OpenAI GPT-3.5**: Inteligência artificial para geração de conteúdo

### **Frontend**
- **HTML5 & CSS3**: Estrutura e estilização moderna
- **Tailwind CSS**: Framework CSS utilitário
- **JavaScript Vanilla**: Interatividade sem dependências pesadas
- **Chart.js**: Gráficos interativos para progresso
- **Design Mobile-First**: Responsivo para todos os dispositivos

### **Segurança**
- **Autenticação Segura**: Hash bcrypt para senhas
- **Proteção CSRF**: Tokens de segurança em formulários
- **Headers de Segurança**: XSS, Clickjacking, CSP
- **Sanitização de Dados**: Validação e limpeza de inputs
- **Sessões Seguras**: Regeneração de IDs e timeouts

### **Performance**
- **Sistema de Cache**: Cache em arquivo para otimização
- **Prepared Statements**: Proteção contra SQL Injection
- **Compressão GZIP**: Redução do tamanho das respostas
- **Minificação**: CSS e JS otimizados

## 📋 Pré-requisitos

- **PHP 8.0+** com extensões:
  - PDO MySQL
  - cURL
  - JSON
  - OpenSSL
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Servidor Web**: Apache 2.4+ ou Nginx 1.18+
- **Composer** (opcional, para dependências futuras)

## 🚀 Instalação

### 1. **Clone o Repositório**
```bash
git clone https://github.com/seu-usuario/vitexa.git
cd vitexa
```

### 2. **Configure o Banco de Dados**
```bash
# Crie o banco de dados MySQL
mysql -u root -p
CREATE DATABASE vitexa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# Importe o schema
mysql -u root -p vitexa_db < database.sql
```

### 3. **Configure as Variáveis de Ambiente**
```bash
# Copie o arquivo de exemplo (ENV, .HTACCESS E CONFIG.PHP)
cp .env.example .env
cp .htaccess.example .htaccess
cp /config/config_example.php /config/config.php

# Edite as configurações (ENV, .HTACCESS E CONFIG.PHP)
nano .env
nano .htaccess
nano /config/config.php
```

Exemplo de configuração `.env`:
```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=vitexa_db
DB_USER=root
DB_PASS=sua_senha

# OpenAI API
OPENAI_API_KEY=sua_chave_openai

# Email (opcional)
MAIL_ENABLED=false
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha

# Cache
CACHE_ENABLED=true
CACHE_TTL=3600
```

### 4. **Execute o Instalador**
```bash
php install.php
```

### 5. **Configure o Servidor Web**

#### **Apache (.htaccess já incluído)**
```apache
<VirtualHost *:80>
    ServerName vitexa.local
    DocumentRoot /path/to/vitexa/public
    
    <Directory /path/to/vitexa/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### **Nginx**
```nginx
server {
    listen 80;
    server_name vitexa.local;
    root /path/to/vitexa/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. **Configure Cron Jobs (Opcional)**
```bash
# Edite o crontab
crontab -e

# Adicione a linha para processar lembretes a cada minuto
* * * * * /usr/bin/php /path/to/vitexa/cron/process_reminders.php
```

## 🎯 Como Usar

Para um guia detalhado sobre como utilizar todas as funcionalidades do Vitexa, consulte a **[Documentação do Usuário](docs/USER_GUIDE.md)**.

## 🔧 Configuração Avançada

Para informações detalhadas sobre a arquitetura do sistema, controladores, modelos, configurações e segurança, consulte a **[Documentação Técnica](docs/DOCUMENTACAO_TECNICA.md)**.

## 🧪 Testes

### **Usuários de Teste**
O sistema vem com usuários pré-configurados:

| Email | Senha | Perfil |
|-------|-------|--------|
| admin@vitexa.com | admin123 | Administrador |
| joao@example.com | password | Usuário padrão |
| maria@example.com | password | Usuária padrão |

### **Testando Funcionalidades**
1. **Autenticação**: Login/logout, cadastro
2. **Dashboard**: Visualização de dados
3. **Geração de Planos**: Treino e dieta via IA
4. **Chat**: Conversas com o assistente
5. **Progresso**: Registro e visualização
6. **Lembretes**: Criação e gerenciamento

## 📁 Estrutura do Projeto

```
vitexa/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ReminderController.php
│   │   ├── ChatController.php
│   │   ├── PlanController.php
│   │   └── UserController.php
│   ├── models/              # Modelos de dados
│   │   ├── Message.php
│   │   ├── Plan.php
│   │   └── User.php
│   ├── views/               # Views (HTML/PHP)
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── plans/
│   │   ├── chat/
│   │   └── reminders/
│   └── core/                # Classes principais
│       ├── Cache.php
│       ├── Controller.php
│       ├── Database.php
│       ├── Model.php
│       ├── Router.php
│       ├── Session.php
│       └── View.php
├── config/
│   └── config.php           # Configurações
├── public/
│   ├── index.php           # Ponto de entrada
│   ├── assets/             # CSS, JS, imagens
│   └── .htaccess           # Configuração Apache
├── cron/
│   └── process_reminders.php # Script de lembretes
├── logs/                   # Logs da aplicação
├── database.sql           # Schema do banco
├── install.php            # Script de instalação
└── README.md              # Este arquivo
```

## 🔒 Segurança

### **Medidas Implementadas**
- **Autenticação**: Hash bcrypt para senhas
- **CSRF Protection**: Tokens em todos os formulários
- **XSS Protection**: Sanitização de dados de entrada
- **SQL Injection**: Prepared statements
- **Headers de Segurança**: CSP, X-Frame-Options, HSTS
- **Rate Limiting**: Proteção contra ataques de força bruta
- **Validação de Dados**: Sanitização e validação rigorosa

### **Boas Práticas**
- Mantenha o PHP e MySQL atualizados
- Use HTTPS em produção
- Configure backups regulares do banco de dados
- Monitore logs de erro e acesso
- Implemente firewall no servidor

## 🚀 Deploy em Produção

### **1. Preparação do Servidor**
```bash
# Instale dependências
sudo apt update
sudo apt install php8.0 php8.0-mysql php8.0-curl php8.0-json mysql-server nginx

# Configure SSL com Let\'s Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d seu-dominio.com
```

### **2. Configuração de Produção**
```php
// Em config/config.php
define(\'APP_ENV\', \'production\');
define(\'APP_DEBUG\', false);
```

### **3. Otimizações**
- Configure cache do PHP (OPcache)
- Use CDN para assets estáticos
- Configure compressão GZIP
- Otimize consultas do banco de dados
- Monitore performance com ferramentas como New Relic

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m \'Add some AmazingFeature\'`) 
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Changelog

### **v1.0.0** (2024-12-19)
- ✅ Sistema de autenticação completo
- ✅ Dashboard interativo com estatísticas
- ✅ Geração de planos de treino via IA
- ✅ Geração de planos de dieta via IA
- ✅ Chatbot especializado em fitness
- ✅ Sistema de progresso com gráficos
- ✅ Lembretes personalizados
- ✅ Cache para otimização de performance
- ✅ Interface mobile-first responsiva

### **v1.0.1** (2025-09-05)
- ✅ Correção do campo de input do chat no mobile
- ✅ Documentação técnica atualizada e expandida
- ✅ Nova documentação para o usuário final

## 📄 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🆘 Suporte

- **Documentação Técnica**: [docs/DOCUMENTACAO_TECNICA.md](docs/DOCUMENTACAO_TECNICA.md)
- **Documentação do Usuário**: [docs/USER_GUIDE.md](docs/USER_GUIDE.md)
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/vitexa/issues)
- **Email**: suporte@vitexa.com
- **Discord**: [Comunidade Vitexa](https://discord.gg/vitexa)

## 🙏 Agradecimentos

- **OpenAI** pela API GPT-3.5 que torna possível a IA personalizada
- **Tailwind CSS** pelo framework CSS que acelera o desenvolvimento
- **Chart.js** pelos gráficos interativos
- **Comunidade PHP** pelas bibliotecas e recursos

---

**Desenvolvido com ❤️ pela equipe Vitexa**

*Transformando vidas através da tecnologia e inteligência artificial aplicada ao fitness e saúde.*