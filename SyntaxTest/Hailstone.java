import javax.swing.JOptionPane;

public class Hailstone
{
	public static void main(String[] args)
	{
		int k = Integer.parseInt(JOptionPane.showInputDialog("Inserisci un intero"));

		for (int n = 1; n <= k; n++)
		{	
			int numero = n;
			
			System.out.print(numero + ": ");
			do
			{
				System.out.print(numero + "->");
				if (numero % 2 == 0)
				{
					numero /= 2;
				}
				else
				{
					numero = (numero * 3) + 1;
				}
			} while (numero != 1);
			
			System.out.print("1\n");
		}
	}
}

			
