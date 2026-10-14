A private key is a secret used to authorize transactions for a particular cryptographic key. A seed phrase is a human-readable wallet recovery backup that can generate many keys in compatible wallets. Losing control of either can put funds at risk, but their scope and recovery roles differ.

## How the pieces fit together

In a typical hierarchical wallet, recovery words and an optional passphrase produce a seed, from which wallet software derives a tree of keys. The words are not a list of the individual private keys. [BIP 39](https://bips.dev/39/) specifies mnemonic generation and conversion to a seed; [BIP 32](https://bips.dev/32/) describes hierarchical deterministic key derivation.

Not every wallet uses these standards. Recovery also depends on wallet compatibility and the information needed to find the right accounts. Before treating a backup as complete, consult the exact wallet’s official recovery instructions.

| Item | Typical role | What to keep in mind |
| --- | --- | --- |
| Seed phrase | Recover a compatible wallet’s derived keys | May expose many accounts if disclosed |
| Private key | Sign for a particular key | Exposure can permit spending from that key |
| Public receiving address | Identify where funds can be received | Does not by itself authorize spending |
| Wallet password or PIN | Unlock a particular app or device | Usually cannot replace a missing recovery backup |

## Why a wallet password is different

Imagine a phone wallet secured with a password and backed up with recovery words. The password protects access through that installation. Recovery words are intended to restore access elsewhere. Changing the app password does not automatically make a disclosed recovery phrase safe.

A BIP 39 passphrase is another distinct concept: combined with the same recovery words, a different passphrase produces a different seed. A forgotten passphrase can therefore prevent recovery of the intended wallet. There may be no “wrong password” warning to identify the intended one. These properties come from the [BIP 39 specification](https://bips.dev/39/), rather than an account-reset service.

## A practical backup example

Suppose your wallet shows a Bitcoin account and several additional addresses. Exporting one address’s private key should not be assumed to back up everything displayed by the app. Conversely, disclosing a wallet’s recovery words can expose more than the account currently visible on screen.

Write a private recovery inventory: wallet name, supported backup format, whether an additional passphrase exists, and where the official recovery instructions are located. Keep secret recovery material separate from an ordinary checklist that might be shared.

Do not publish real words or keys in a support ticket, screenshot, chatbot conversation, or online backup-checking form. A service asking for a recovery phrase to “synchronize” or “validate” a wallet is asking for a secret that can grant control.

## What if a secret has been exposed?

Treat a copied secret as compromised even if no unauthorized transaction is visible yet. Locking an app or changing its PIN does not undo knowledge of the underlying key material.

For funds you control, recovery generally involves establishing a fresh, securely generated wallet and moving assets away from compromised keys where possible. The exact procedure depends on the network and wallet. Obtain instructions from the wallet provider’s verified documentation; do not follow links sent by someone who claims they can recover funds for a fee.

For hierarchical wallets, key relationships can matter too. BIP 32 documents situations involving a non-hardened child private key and a parent extended public key that can expose additional keys. Avoid assuming that every leaked individual key is isolated from the rest of a wallet.

## Frequently asked questions

### Can I recover my wallet with only its address?

A receiving address is public information. It does not supply the private signing material needed to recover spending control.

### Can customer support reset my seed phrase?

A self-custody recovery phrase is not a conventional account password. A support representative should not need your phrase to troubleshoot an app.

### Is a screenshot a good backup?

A screenshot creates another digital copy that may sync to cloud storage or other devices. Choose a backup approach based on the wallet’s official guidance and your ability to protect it from both loss and unauthorized access.

## Continue learning

Next, understand the cost of sending a transaction in [Ethereum gas fees explained](/read/ethereum-gas-fees-explained). Keeping keys safe and understanding transaction details are separate parts of using a wallet carefully.
