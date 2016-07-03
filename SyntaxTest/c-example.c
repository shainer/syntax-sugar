#include <stdio.h>

#define LEN 21

void printMatrice(unsigned long long m[][LEN])
{
	int i,j;
	
	for (i = 0; i < LEN; ++i)
	{
		for (j = 0; j < LEN; ++j)
		{
			printf ("%llu ", m[i][j]);
		}
		printf("\n");
	}
}

int main(int argc, char **argv)
{
	unsigned long long matrice[LEN][LEN] = {0};
	int i, j;
	
	for (i = 0; i < LEN; ++i)
	{
		matrice[i][0] = matrice[0][i] = 1;
	}
	matrice[0][0] = 0;
	
	for (i = 1; i < LEN; ++i)
	{
		for (j = 1; j < LEN; ++j)
		{
			matrice[i][j] = matrice[i-1][j] + matrice[i][j-1];
		}		
	}
	
	printMatrice(matrice);
	return 0;
}

