# geweb-manual

Central de manuais da Geweb em PHP.

## Estrutura

- `app`: arquivos PHP da aplicacao.
- `assets/css`: estilos da interface.
- `assets/js`: scripts do front-end.
- `assets/img`: imagens usadas pela tela.
- `storage/data`: arquivo JSON com manuais gerais e manuais por empresa.
- `storage/sessions`: arquivos temporarios de sessao.
- `storage/uploads`: PDFs enviados pelo cadastro de manuais.

## Regras

- Usuarios iniciados por `geweb` podem cadastrar manuais.
- Usuarios comuns visualizam manuais, baixam PDFs e acessam treinamentos do YouTube.
- Manuais podem ser gerais ou vinculados a uma empresa especifica.

