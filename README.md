# Jogo dos pontos com IA
Este jogo foi desenvolvido para a matéria de Inteligência Artificial do curso de Engenharia da Computação da UNISOCIESC.
A lógica é processada em um servidor desenvolvido em PHP, a interface foi desenvolvida utilizando HTML/JS/CSS e a comunicação é feita através de WebSockets.

![Cliente](/client/public/assets/images/screenshot.png?raw=true)

![Servidor](/client/public/assets/images/screenshot2.png?raw=true)

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

Iniciar o servidor/cliente
```
docker-composer up
```

Ao acessar [http://localhost:5556](http://localhost:5556) o jogo estará disponível.
