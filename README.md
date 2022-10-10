# Getting Started

* Deploy

- Colocar o composer.json disponibilizado pela equipe de desenvolvimento na raiz da aplicação

- Rodar o composer install pra instalar uma instalação básica do drupal

- Copiar a pasta /sites/ disponibilizada pela equipe de desenvolvimento e substituir pela existente da instalação básica

- Acessar o arquivo futuros_possiveis/web/sites/sites.php e alterar o HOST da linha 59 pela da aplicação no servidor desejado. (ex: $sites['futurospossiveis2022.lndo.site'])

- Acessar o arquivo futuros_possiveis/web/sites/futurospossiveis2022.com.br/settings.php e alterar os dados da linha 771 pelos dados do servidor atual

- Acessar a aplicação pelo servidor e rodar a instalação no banco normalmente. 
*OBS: não vai pedir banco no wizard por que já tá configurado no settings.*

- Certificar-se que o drupal está rodando o básico e a instalação ok. Se sim, dar um DROP TABLE no banco do servidor e subir o dump no mesmo. 

- Testar a aplicação novamente.


