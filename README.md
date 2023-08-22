# PBL Manager
O **PBL Manager** é uma aplicação web baseada em PHP Laravel projetada para simplificar o gerenciamento e a aplicação da metodologia de Aprendizagem Baseada em Problemas (PBL - Problem-Based Learning). Esta ferramenta permite que educadores e alunos criem, acompanhem e colaborem em projetos baseados em problemas de forma eficaz.





## Requisitos de Sistema
Antes de começar a configurar e executar o **PBL Manager**, certifique-se de que seu sistema atende aos seguintes requisitos:

- PHP (versão 7.4 ou superior)
- Composer (gerenciador de pacotes PHP)
- Banco de dados livre (pode ser usado o MySQL)

## Instalação e Configuração

1. Clone o repositório do GitHub para sua máquina:
```
git clone https://github.com/seu-usuario/pbl-manager.git
cd pbl-manager
```
2. Instale as dependências do PHP usando o Composer:
```
composer install
```
3. Crie um arquivo de ambiente .env na raiz do projeto e configure suas credenciais de banco de dados. Você pode usar o arquivo .env.example como referência:
```
DB_CONNECTION=pgsql
DB_HOST=seu-host
DB_PORT=sua-porta
DB_DATABASE=nome-do-banco
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```
4. Gere a chave de criptografia do aplicativo Laravel:
```
php artisan key:generate
```
5. Execute as migrações do banco de dados para criar as tabelas necessárias:
```
php artisan migrate:fresh --seed
```
6. Inicie o servidor do PBL Manager:
```
php artisan serve
```

## Uso
Após a configuração bem-sucedida, você pode começar a usar o PBL Manager acessando-o no navegador. Inicialmente, você pode fazer login com as credenciais padrão (ou criar uma nova conta)
Estes usuários podem ser visto na pasta de **seeds** :

- **Usuário Administrador**
    - Email: adm@adm
    - Senha: adm12345

- **Usuário Professor**
    - Email: roberto@tutor
    - Senha: roberto12345

- **Usuário Aluno**
    - Email: icaro@aluno
    - Senha: icaro123

## Contribuição
Se você deseja contribuir para o desenvolvimento do PBL Manager, siga estas diretrizes:

1. Crie um fork do repositório.
2. Crie uma nova branch com a seguinte nomenclatura:

```
git checkout -b feature/sua-feature
Desenvolva sua feature ou correção de bug.
```

3. Faça commit das alterações e envie para o seu fork:

```
git commit -m "Adiciona nova funcionalidade"
git push origin feature/sua-feature
```
4. Abra um pull request para a branch principal deste repositório ou fique livre para usar a fork que você criou.


