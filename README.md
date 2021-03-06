# Waller
## Entities
* User
  * JuridicalPerson
  * NaturalPerson
* Transaction
* Balance
* Money (ValueObject)

## Problems
* The initial seed of transactions. "Someone has to pay somebody", because of this statement, there will be an user or users that will have negative amount on its balance. We can accept this or change the data model so we can have transaction where only the payee is declared, creating money out of nowhere so we can have some to start transactioning or so we can inject more money inside the wallet platform.

# Falta
- Refatorar Balance::buildFromUser
- Refatorar TransactionController -  Remover lógica de validação e criação de transação
- Testes Testes Testes
- Documentação
  - Explicar como rodar o projeto localmente
  - Explicar como subir projeto pra produção ?
    - Vercel?
    - Dockerfile?
- Endpoint de depósito?
- Soma de transações para um usuário?
