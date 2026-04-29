# Convites de workspace

A gestao de equipe agora possui um fluxo de convite compartilhavel.

## Fluxo

1. Owner ou admin informa e-mail e papel no dashboard.
2. Se o usuario ja existe, ele entra diretamente no workspace.
3. Se nao existe, o sistema cria um convite pendente com token unico e validade de 14 dias.
4. O sistema tenta enviar e-mail transacional com o link do convite.
5. Se o envio falhar, o erro fica registrado no convite e o link continua disponivel para suporte/manual.
6. Ao abrir o link, o usuario pode entrar ou criar conta com o mesmo e-mail.
7. Ao aceitar, o membro e vinculado ao workspace e o convite vira `accepted`.

## Pendencia futura

Quando escolhermos provedor definitivo de e-mail transacional, devemos configurar SMTP/API em producao e criar templates HTML oficiais.

## Valor para lancamento

Esse fluxo reduz friccao para times: o owner nao precisa compartilhar senha, secret ou conta pessoal. Cada dev entra com sua identidade e papel.