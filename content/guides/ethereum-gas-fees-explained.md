Ethereum gas measures computational work. For a typical EIP-1559 execution transaction, the actual execution fee equals gas used multiplied by the effective price per gas. The gas limit caps permitted work; it is not necessarily the amount ultimately charged.

## The terms on a wallet fee screen

Gas used is the work charged for the transaction. The gas limit is the maximum execution gas the transaction permits. Gwei is a denomination: one gwei equals one billionth of an ETH.

The base fee is a protocol-determined amount that is burned. A priority fee can compensate the validator. With EIP-1559, max fee per gas caps the total permitted gas price, while max priority fee per gas caps its priority component. The [Ethereum gas documentation](https://ethereum.org/developers/docs/gas/) explains these components.

| Term | What it represents |
| --- | --- |
| Gas used | Charged execution work |
| Gas limit | Maximum permitted execution work |
| Base fee | Protocol-set component per gas |
| Max priority fee | Cap on the validator tip per gas |
| Max fee | Cap on the combined price per gas |

## How the effective gas price works

For an includable EIP-1559 transaction, the priority component is limited by both the chosen priority cap and the room left under the total fee cap. In simplified notation:

Effective gas price = base fee + minimum(max priority fee, max fee − base fee).

The total fee cap must at least cover the relevant base fee for inclusion. The [EIP-1559 specification](https://eips.ethereum.org/EIPS/eip-1559) defines the fee-cap rules. Raising a cap permits a higher charge; it does not mean the whole cap is automatically spent.

## A hypothetical calculation

Suppose a transaction uses 21,000 gas, the base fee is 20 gwei, the priority cap is 2 gwei, and the total cap is 30 gwei. The effective gas price is 22 gwei. The execution fee is:

21,000 × 22 gwei = 462,000 gwei = 0.000462 ETH.

At an invented ETH price of $2,000, that would equal $0.924 before any separate service charges. These figures illustrate arithmetic, not current network conditions or a live quote.

Do not assume every transaction uses 21,000 gas. Contract calls and other execution can require more. Use the receipt’s actual gas-used and effective-gas-price fields to reconcile an execution fee after inclusion.

## Why a failed transaction can still cost money

An included transaction can perform work before execution fails. Reverting the intended state changes does not mean that no computation happened. Ethereum’s [transaction documentation](https://ethereum.org/developers/docs/transactions) explains transaction fields and processing.

Distinguish an included failed transaction from a request rejected before broadcast or a transaction that never gets included. A wallet error message alone does not establish which case occurred. Inspect the network and transaction receipt rather than assuming every unsuccessful attempt incurred the same charge.

## How to interpret an expensive estimate

First identify the network. An Ethereum mainnet transaction and a transaction on a layer-2 network can have different fee accounting. Ethereum’s [layer-2 overview](https://ethereum.org/layer-2/) explains how these networks scale activity; a bridge can add additional transactions and risks.

Next identify the action: sending ETH, approving a token, swapping, and bridging are different operations. Check whether the app includes service fees in its displayed total. Compare estimates for the same action and network before concluding that two wallets charge different network fees.

If timing is flexible, observing estimates at another time can help you understand demand changes. Lowering a fee cap may delay inclusion or prevent inclusion while the base fee exceeds that cap. Lowering a gas limit below what execution needs is a different change and can lead to failure.

## Frequently asked questions

### Is the gas limit the final fee?

No. It is measured in gas units. The final execution fee also depends on actual gas used and the effective price per gas.

### Is unused execution gas automatically spent?

The execution fee is based on charged gas use rather than automatically consuming the entire stated gas limit. Distinguish this from refunds for specific execution operations and from separate app fees.

### Can I pay mainnet execution gas with any token?

Ethereum protocol execution fees are denominated in ETH. Some wallet services abstract payment or sponsor transactions, but their user-facing arrangement does not change the underlying protocol denomination.

## Continue learning

Protect the signing credentials behind every transaction with [seed phrase versus private key](/read/seed-phrase-vs-private-key), and compare trading costs in [liquidity versus volume](/read/crypto-liquidity-vs-volume).
