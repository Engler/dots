# Jogo dos pontos com IA
Este jogo foi desenvolvido para a matéria de Inteligência Artificial do curso de Engenharia da Computação da UNISOCIESC.
A lógica é processada em um servidor desenvolvido em PHP, a interface foi desenvolvida utilizando HTML/JS/CSS e a comunicação é feita através de WebSockets.

## Autores
* Guilherme Engler Stadelhofer ([@Engler](http://github.com/Engler))
* Elton Henrique Faust ([@EltonFaust](http://github.com/EltonFaust))

## Instalação

Clonar este repositório
```
git clone https://github.com/Engler/dots.git dots-game
```

Acessar a pasta do servidor e instalar as dependências através do Composer
```
cd dots-game/server
composer install
```

Iniciar o servidor
```
cd dots-game/server/bin
php start-server
```

Para acessar o jogo, basta servir a pasta **client/public**
```
cd dots-game/client/public
php -S localhost:8000
```

Ao acessar [http://localhost:8000](http://localhost:8000) o jogo estará disponível.

## Estratégia da IA
