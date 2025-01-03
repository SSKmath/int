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

function parseInput(inputString) 
{
    const step_k = new Map();
    inputString = removeSpaces(inputString);
    inputString = inputString.replace(/-/g, '+-');
    const arr = inputString.split('+');

    for (let i = 0; i < arr.length; i++) 
    {
        if (arr[i].includes('x')) 
        {
            const tempArr = arr[i].split('x');
            let k = tempArr[0], step = tempArr[1];
            let rezK = 0, rezStep = 0;
            
            if (k.length === 0)
                rezK = 1;
            else if (k === '-')
                rezK = -1;
            else
                rezK = parseInt(k, 10);

            if (step.length === 0)
                rezStep = 1;
            else
                rezStep = parseInt(step, 10);

            if (step_k.has(rezStep))
                step_k.set(rezStep, step_k.get(rezStep) + rezK);
            else
                step_k.set(rezStep, rezK);
        } 
        else 
        {
            const value = parseInt(arr[i], 10);
            if (step_k.has(0))
                step_k.set(0, step_k.get(0) + value);
            else
                step_k.set(0, value);
        }
    }

    return step_k;
}

function getLastAndFirstK(step_k)
{
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
    return [starhsiyK, svobodniyK];
}

function main(inputString) 
{
    const step_k = parseInput(inputString);
    
    console.log(step_k);

    const [starhsiyK, svobodniyK] = getLastAndFirstK(step_k);

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
                const nd = nod(Math.abs(a), b);
                a /= nd;
                b /= nd;
                if (b === 1)
                    korni.add(a.toString());
                else
                    korni.add(a.toString() + '/' + b.toString());
            }
        }
    }

    if (Math.abs(solve(step_k, 0)) < 0.0001)
        korni.add('0');

    let rezultat = '';
    for (const pair of korni)
        rezultat += pair + ' ';

    return rezultat;
}

document.getElementById('button').addEventListener('click', function() {
    let inputString = document.getElementById("input").value;
    document.getElementById("output").innerText = main(inputString);
});