﻿<h1 align="center">SellCar</h1>

<h2>Logo da Página:</h2>

<img src="images/sellcar 500x500.png" alt="Logo da Página" height="250" width="250">

<h2>Cores da Página:</h2>

- Branco claro: `#ffffff`
- Branco mais escuro: `#ecedf2`
- Vermelho (vinho): `#520002`
- Cor do texto preto: `#000000`
- Cor do texto branco: `#ffffff`

<h2>Fontes da Página:</h2>

Padrão das fontes usadas foi dada pela seguinte tuplas: (font-family), (font-weight), (font-size), (letter-spacing).

- Texto do header: `Poppins, regular, 20px, 1px`
- Texto bold do header: `Poppins, 600, 20px , 1px`
- Texto card carro: `Poppins, regular, 18px, 1px`
- Texto card cidade: `Poppins, semibold, 18px, 1px`
- Texto detalhes do anúncio: `Poppins, regular, 36px, 1px`
- Texto detalhes do anúncio (semi bold): `Poppins, semibold, 550, 1px`
- Texto footer: `Poppins, 600, 900, 1px`

<h2>Telas:</h2>

[Projeto no Figma](https://www.figma.com/design/ieuP0RSjxkkAqmn9j0dz4Y/SellCar?node-id=0-1&t=xoTmU9SrXtjWqAoy-1)

<h3>Página Principal Externa (Pública):</h3>

<img src="images/Tela Principal Externa.png" alt="Imagem Tela Principal Externa">

A equipe deverá criar um nome fictício para o portal de anúncios, assim como um **logotipo**, os quais deverão ser exibidos na página principal utilizando um **layout moderno e bem estruturado**. A página principal deverá exibir um painel de buscas com campos do tipo **select** para que o usuário possa informar a **marca do veículo de interesse, o modelo e a localização (cidade)**. Abaixo do painel de busca deverão aparecer, no **formato de cards**, apenas os veículos anunciados que atendem aos critérios informados pelo usuário. Para a primeira entrega não é necessário implementar efetivamente a funcionalidade de busca, pois não haverá comunicação com banco de dados. **Basta exibir uma lista estática de cards de anúncios** (**cada card deve conter a marca, o modelo, o ano de fabricação, uma foto, a cidade e o valor de venda do veículo anunciado**).

<h3>Página de Cadastro de Usuário (Anunciante)</h3>

O portal deve disponibilizar uma página contendo um **formulário** para **cadastro de novos usuários**. Os
seguintes dados devem ser cadastrados: **nome, CPF, e-mail, senha e telefone**.

<img src="images/Página de Cadastro de Usuário.png" alt="Página de Cadastro de Usuário">

<h3>Página de Login</h3>

Página contendo um **formulário de login** com os **campos e-mail e senha** para que um usuário que já
tenha se cadastrado possa logar e criar seus anúncios no sistema. **A ação de logar em si só deve ser implementada no sistema final**.

<img src="images/Página de Login.png" alt="Página de Login">

<h3>Página de Registro de Interesse no Veículo Anunciado</h3>

Página para que um **usuário interessado em um veículo anunciado possa deixar sua mensagem de interesse no veículo**. A página deve permitir que o **usuário informe seu nome, telefone e a mensagem de interesse propriamente dita**.

<img src="images/Página de Registro de Interesse no Veículo Anunciado.png" alt="Página de Registro de Interesse no Veículo Anunciado">

<h3>Página Principal Interna (Restrita)</h3>

Página principal da área restrita que será **apresentada ao usuário depois que o login for realizado com sucesso**. Esta página deve **conter links e/ou botões que dêem acesso às funcionalidades da parte restrita como criação de novo anúncio, listagem dos anúncios criados etc**. **Deverá haver uma opção para que o usuário realize logoff**.

<img src="images/Página Principal Interna (restrita).png" alt="Página Principal Interna (restrita)">

<h3>Página Para Criação de Anúncios de Veículo (Restrita)</h3>

Esta página deve conter um **formulário que permita ao usuário criar novos anúncios de veículos**. Os dados a serem cadastrados são: **marca do veículo, modelo, ano de fabricação, cor, quilometragem, descrição, valor, estado (MG, SP, RJ etc.), cidade e fotos do veículo (pelo menos três)**.

<img src="images/Página para Criação de Anúncio de Veículo (restrita).png" alt="Página para Criação de Anúncio de Veículo (restrita)">

<h3>Página de Listagem de Anúncios (Restrita)</h3>

Esta página deve **listar, de maneira resumida, todos os anúncios criados pelo usuário logado**. Para cada anúncio deverá ser apresentado **uma foto do veículo, a marca, o modelo, o ano de fabricação, um botão/link para visualização detalhada do anúncio, um botão/link para visualização dos interesses no anúncio e um botão/link para exclusão do anúncio**.

<img src="images/Página de Listagem de Anúncios (restrita).png" alt="Página de Listagem de Anúncios (restrita)">

<h3>Página de Exibição Detalhada do Anúncio (Restrita)</h3>

Quando o usuário clicar no **link/botão de visualização detalhada do anúncio, na listagem anterior**, deverá ser **apresentada uma página contendo todos os dados e fotos do respectivo veículo anúnciado.**

<img src="images/Página para Exibição Detalhada do Anúncio (restrita).png" alt="Página para Exibição Detalhada do Anúncio (restrita)">

<h3>Página de Listagem de Interesses (Restrita)</h3>

Quando o usuário clicar no **botão/link de visualização dos interesses do anúncio, na listagem anterior, deverá ser apresentada uma página contendo todas as informações de todos os interesses registrados pelos internautas no respectivo anúncio (nome, telefone e mensagem de interesse em si).**

<img src="images/Página de Listagem de Interesses (restrita).png" alt="Página de Listagem de Interesses (restrita)">

<h2>Como Contribuir:</h2>

Para clonar o repositório e utilizar, execute:

```bash
git clone https://github.com/RafaLima14028/SellCar.git
cd SellCar
```

Para fazer um commit, execute:

```bash
git pull
git add .
git commit -m "mensagem do commit"
git push
```

<h2>Contribuidores:</h2>

Este projeto foi desenvolvido como parte de um trabalho de faculdade. Agradecemos a contribuição de todos os membros da equipe:

- [Rafael](https://github.com/RafaLima14028)
- [Gabriel](https://github.com/gkatog)
- [Lucas](https://github.com/LucasDaniel1)

<h2>Correções para fazer</h2>

- Colocar o valor correto de ©
- Colocar o label em inputs que não tem
- Verificar se tudo está direcionando para as páginas corretamente
