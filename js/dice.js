// O resultado é calculado uma vez; a animação apenas o apresenta.
function rollDice(sides, count) {
    const values = [];
    for (let index = 0; index < count; index++) {
        values.push(Math.floor(Math.random() * sides) + 1);
    }
    return { values, total: values.reduce((sum, value) => sum + value, 0) };
}

function arcanaDice(root, onRoll) {
    root.innerHTML = `
        <div class="picker" aria-label="Tipo de dado"></div>
        <label>Quantidade <input type="number" min="1" max="10" value="1"></label>
        <div class="tray"><span class="die" aria-hidden="true">20</span></div>
        <button class="roll" type="button">Rolar ↗</button>
        <p role="status" aria-live="polite"></p>
    `;
    let sides = 20;
    const picker = root.querySelector(".picker");
    for (const number of [4, 6, 8, 10, 12, 20, 100]) {
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = "D" + number;
        button.setAttribute("aria-pressed", String(number === sides));
        button.onclick = () => {
            sides = number;
            picker.querySelectorAll("button").forEach(item => {
                item.setAttribute("aria-pressed", String(item === button));
            });
        };
        picker.append(button);
    }

    const rollButton = root.querySelector(".roll");
    rollButton.onclick = async () => {
        const input = root.querySelector('input[type="number"]');
        if (!input.reportValidity()) return;
        const count = Number(input.value);
        rollButton.disabled = true;
        const die = root.querySelector(".die");
        die.classList.add("rolling");
        try {
            const result = onRoll ? await onRoll(sides, count) : rollDice(sides, count);
            await new Promise(resolve => setTimeout(resolve, 650));
            die.textContent = result.total;
            root.querySelector('p[role="status"]').textContent =
                result.values.join(" + ") + " = " + result.total;
        } catch (error) {
            root.querySelector('p[role="status"]').textContent = "Não foi possível rolar. Tente novamente.";
        } finally {
            die.classList.remove("rolling");
            rollButton.disabled = false;
        }
    };
}

document.querySelectorAll("[data-standalone-dice]").forEach(root => arcanaDice(root));
