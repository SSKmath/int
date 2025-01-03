function removeSpaces(str) 
{
    return str.split(' ').join('');
}
function getDivisors(n, includeNegative = false) 
{
    const divisors = [];
    const absN = Math.abs(n);
    for (let i = 1; i <= absN; i++) 
    {
        if (n % i === 0) {
            divisors.push(i);
            if (includeNegative)
                divisors.push(-i);
        }
    }
    return divisors;
}

function solve(step_k, x) 
{
    let y = 0;
    for (const [key, value] of step_k)
    {
        if (key === 0)
            y += value;
        else
            y += value * Math.pow(x, key);
    }
    return y;
}

function nod(a, b) 
{
    while (b !== 0) 
    {
        const t = b;
        b = a % b;
        a = t;
    }
    return a;
}

function isDigit(char) 
{
    return /d/.test(char);
}

function parseInput(inputString) {
    const step_k = new Map();
    inputString = removeSpaces(inputString);

    for (let i = 0; i < inputString.length; i++) {
        if (inputString[i] === 'x') {
            let lenStep = 1;
            while (i + lenStep < inputString.length && !isNaN(inputString[i + lenStep])) {
                lenStep++;
            }
            lenStep--;

            let lenK = 1;
            while (i - lenK >= 0 && !isNaN(inputString[i - lenK])) {
                lenK++;
            }
            lenK--;

            let step, k;

            if (lenStep === 0) {
                step = 1;
            } else {
                step = parseInt(inputString.substr(i + 1, lenStep), 10);
            }

            if (lenK === 0) {
                if (i > 0 && inputString[i - 1] === '-') {
                    k = -1;
                    lenK++;
                } else {
                    k = 1;
                    if (i - 1 >= 0 && inputString[i - 1] === '+') {
                        lenK++;
                    }
                }
            } else {
                if (i - lenK > 0 && inputString[i - lenK - 1] === '-') {
                    k = -parseInt(inputString.substr(i - lenK, lenK), 10);
                    lenK++;
                } else {
                    k = parseInt(inputString.substr(i - lenK, lenK), 10);
                    if (i - lenK - 1 >= 0 && inputString[i - lenK - 1] === '+') {
                        lenK++;
                    }
                }
            }

            step_k.set(step, k);
            inputString = inputString.substr(0, i - lenK) + inputString.substr(i + lenStep + 1);
            i = 0;
        }
    }

    if (inputString.length > 0) {
        step_k.set(0, parseInt(inputString, 10));
    }

    return step_k;
}

function main(inputString) 
{
    let step_k = parseInput(inputString);

    let ft1 = true;
    let starhsiyK = 0;
    let svobodniyK = 0;
    for (const [key, value] of step_k)
    {
        if (ft1)
        {
            ft1 = false;
            starhsiyK = value;
        }
        svobodniyK = value;
        /*console.log(key.toString() + ' ' + value.toString());*/
    }

    const chisl = getDivisors(svobodniyK, true);
    const znam = getDivisors(starhsiyK);

    let korni = new Set();

    for (let i = 0; i < znam.length; i++) 
    {
        for (let j = 0; j < chisl.length; j++) 
            {
            if (Math.abs(solve(step_k, chisl[j] / znam[i])) < 0.0001) {
                let a = chisl[j];
                let b = znam[i];
                const nd = nod(a, b);
                a /= nd;
                b /= nd;
                korni.add(a.toString() + '/' + b.toString());
            }
        }
    }

    if (Math.abs(solve(step_k, 0)) < 0.0001)
        korni.add('0/1');

    let rezultat = '';
    for (const pair of korni)
        rezultat += pair + ' ';

    return rezultat;
}

document.getElementById('button').addEventListener('click', function() {
    let inputString = document.getElementById("input").value;
    document.getElementById("output").innerText = main(inputString);
});