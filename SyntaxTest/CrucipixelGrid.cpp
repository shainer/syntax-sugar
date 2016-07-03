#include "CrucipixelGrid.h"

IMPLEMENT_DYNAMIC_CLASS(CrucipixelGrid, wxControl);

BEGIN_EVENT_TABLE(CrucipixelGrid, wxControl)
	EVT_LEFT_DOWN(CrucipixelGrid::OnClick)
	EVT_RIGHT_DOWN(CrucipixelGrid::OnClick)
	EVT_PAINT(CrucipixelGrid::OnPaint)
END_EVENT_TABLE()


#define MINIMUM_SIDE               10
#define THICK_WIDTH                3
#define SLIM_WIDTH                 1

/** @brief ResetValues
  *
  * @todo: document this function
  */
void CrucipixelGrid::ResetValues()
{
	p_Loading = false;
	p_Initialized = false;

	p_Side = MINIMUM_SIDE;
	p_Columns = 0;
	p_Rows = 0;
	p_MaxColumnBlocks = 0;
	p_MaxRowBlocks = 0;
}

/** @brief Initialize
  *
  * @todo: document this function
  */
void CrucipixelGrid::Initialize(CrucipixelFile &in)
{
	FreeGrid();
	ClearBlockInfo();

	SetColumns(in.p_Columns);
	SetRows(in.p_Rows);

	p_Blocks.columns = in.p_ColumnBlocks;
	p_Blocks.rows = in.p_RowBlocks;

	UpdateBlockInfo();
	UpdateSide();
	UpdatePosition();

	p_Initialized = true;

	AllocateGrid();
	if (!p_Loading)
	{
		Refresh();
	}
}

/** @brief ClearBlockInfo
  *
  * @todo: document this function
  */
void CrucipixelGrid::ClearBlockInfo()
{
	unsigned i;

	for (i = 0; i < p_Blocks.columns.size(); ++i)
	{
		p_Blocks.columns[i].clear();
	}
	p_Blocks.columns.clear();

	for (i = 0; i < p_Blocks.rows.size(); ++i)
	{
		p_Blocks.rows[i].clear();
	}
	p_Blocks.rows.clear();
}

/** @brief OnClick
  *
  * @todo: document this function
  */
void CrucipixelGrid::OnClick(wxMouseEvent &event)
{
	if (!p_Initialized)
	{
		event.Skip();
		return;
	}

	wxCoord x, y;

	event.GetPosition(&x, &y);

	x -= wxCoord(p_Position.x + p_Position.xOffset);
	x /= p_Side;

	y -= wxCoord(p_Position.y + p_Position.yOffset);
	y /= p_Side;

	if (x < wxCoord(p_Columns) && y < wxCoord(p_Rows))
	{
		if (event.LeftDown())
		{
			if (p_Grid[x][y] != Filled)
			{
				p_Grid[x][y] = Filled;
			}
			else
			{
				p_Grid[x][y] = Empty;
			}
		}
		else if (event.RightDown())
		{
			if (p_Grid[x][y] != Marked)
			{
				p_Grid[x][y] = Marked;
			}
			else
			{
				p_Grid[x][y] = Empty;
			}
		}

		Refresh();
	}
}

/** @brief OnPaint
  *
  * @todo: document this function
  */
void CrucipixelGrid::OnPaint(wxPaintEvent &WXUNUSED(event))
{
	wxPaintDC paintdc(this);
	wxGCDC dc(paintdc);

	dc.SetBackground(*wxWHITE_BRUSH);
	dc.Clear();

	if (p_Initialized)
	{
		UpdateSide();
		UpdatePosition();

		DrawGrid(dc);
	}
	else
	{
		wxString msg = wxT("Nessun crucipixel caricato.");
		wxSize size = GetSize();
		wxCoord x, y;
		wxCoord w, h;

		dc.GetTextExtent(msg, &w, &h);

		x = (size.GetWidth() - w) / 2;
		y = (size.GetHeight() - h) / 2;
		dc.DrawText(msg, x, y);
	}
}

/** @brief DrawGrid
  *
  * @todo: document this function
  */
void CrucipixelGrid::DrawGrid(wxDC &dc)
{
	wxPen thickPen(*wxBLACK, THICK_WIDTH);
	wxPen slimPen(*wxBLACK, SLIM_WIDTH);
	unsigned size;
	unsigned i, j, k, w;

	thickPen.SetJoin(wxJOIN_MITER);
	slimPen.SetJoin(wxJOIN_MITER);

	/* Contour */
	dc.SetPen(thickPen);
	dc.DrawRectangle((p_Position.x + p_Position.xOffset), (p_Position.y + p_Position.yOffset),
		(p_Position.width - p_Position.xOffset) - (THICK_WIDTH / 2), (p_Position.height - p_Position.yOffset) - (THICK_WIDTH / 2));

	/* Columns */
	for (i = 0, k = (p_Position.x + p_Position.xOffset); (i <= p_Columns); ++i, k += p_Side)
	{
		dc.SetPen(slimPen);
		if ((i % 5 == 0) || (i == p_Columns))
		{
			dc.SetPen(thickPen);
		}

		dc.DrawLine(k, p_Position.y, k, (p_Position.y + p_Position.height) - SLIM_WIDTH);
	}

	/* Rows */
	for (i = 0, k = (p_Position.y + p_Position.yOffset); (i <= p_Rows); ++i, k += p_Side)
	{
		dc.SetPen(slimPen);
		if ((i % 5 == 0) || (i == p_Rows))
		{
			dc.SetPen(thickPen);
		}

		dc.DrawLine(p_Position.x, k, (p_Position.x + p_Position.width) - SLIM_WIDTH, k);
	}

	dc.SetPen(wxNullPen);
	dc.SetBrush(*wxBLACK_BRUSH);

	/* Grid */
	for (i = 0; i < p_Columns; ++i)
	{
		wxCoord x = (p_Position.x + p_Position.xOffset) + (i * p_Side);

		for (k = 0; k < p_Rows; ++k)
		{
			wxCoord y = (p_Position.y + p_Position.yOffset) + (k * p_Side);

			if (p_Grid[i][k] == Filled)
			{
				dc.DrawRectangle(x, y, p_Side, p_Side);
			}
			else if (p_Grid[i][k] == Marked)
			{
				dc.DrawCircle(x + (p_Side / 2), y + (p_Side / 2), 2);
			}
		}
	}

	/* Columns blocks */
	for (i = 0, k = (p_Position.x + p_Position.xOffset) + (i * p_Side); (i < p_Columns); ++i, k += p_Side)
	{
		size = p_Blocks.columns[i].size();

		for (j = 0, w = (p_Position.y + p_Position.yOffset) - (size * p_Side); j < size; ++j, w += p_Side)
		{
			wxString txt;

			txt << p_Blocks.columns[i][j];
			DrawTextCentered(dc, txt, k, w, p_Side, p_Side);
		}
	}

	/* Rows blocks */
	for (i = 0, k = (p_Position.y + p_Position.yOffset) + (i * p_Side); (i < p_Rows); ++i, k += p_Side)
	{
		size = p_Blocks.rows[i].size();

		for (j = 0, w = (p_Position.x + p_Position.xOffset) - (size * p_Side); j < size; ++j, w += p_Side)
		{
			wxString txt;

			txt << p_Blocks.rows[i][j];
			DrawTextCentered(dc, txt, w, k, p_Side, p_Side);
		}
	}

	dc.SetBrush(wxNullBrush);
}

/** @brief DrawTextCentered
  *
  * @todo: document this function
  */
void CrucipixelGrid::DrawTextCentered(wxDC &dc, wxString &txt, wxCoord x, wxCoord y, unsigned uWidth, unsigned uHeight)
{
	wxCoord dx, dy;
	wxCoord w, h;

	dc.GetTextExtent(txt, &w, &h);
	dx = (uWidth - w) / 2;
	dy = (uHeight - h) / 2;
	dc.DrawText(txt, x + dx, y + dy);
}

/** @brief GetRows
  *
  * @todo: document this function
  */
unsigned CrucipixelGrid::GetRows()
{
	return p_Rows;
}

/** @brief GetColumns
  *
  * @todo: document this function
  */
unsigned CrucipixelGrid::GetColumns()
{
	return p_Columns;
}

/** @brief SetRows
  *
  * @todo: document this function
  */
void CrucipixelGrid::SetRows(unsigned uHeight)
{
	p_Rows = uHeight;
}

/** @brief SetColumns
  *
  * @todo: document this function
  */
void CrucipixelGrid::SetColumns(unsigned uWidth)
{
	p_Columns = uWidth;
}

/** @brief UpdateSide
  *
  * @todo: document this function
  */
void CrucipixelGrid::UpdateSide()
{
	wxSize size = GetSize();
	unsigned uSideX, uSideY;

	uSideX = (size.GetWidth() / (p_Columns + p_MaxColumnBlocks + 1));
	uSideY = (size.GetHeight() / (p_Rows + p_MaxRowBlocks + 1));

	p_Side = wxMax(MINIMUM_SIDE, wxMin(uSideX, uSideY));
}

/** @brief UpdatePosition
  *
  * @todo: document this function
  */
void CrucipixelGrid::UpdatePosition()
{
	wxSize size = GetSize();

	p_Position.xOffset = (p_Side * p_MaxRowBlocks);
	p_Position.width = (p_Side * p_Columns) + p_Position.xOffset + 1;

	p_Position.yOffset = (p_Side * p_MaxColumnBlocks);
	p_Position.height = (p_Side * p_Rows) + p_Position.yOffset + 1;

	p_Position.x = (size.GetWidth() - p_Position.width) / 2;
	p_Position.y = (size.GetHeight() - p_Position.height) / 2;
}

/** @brief UpdateBlockInfo
  *
  * @todo: document this function
  */
void CrucipixelGrid::UpdateBlockInfo()
{
	unsigned i;

	p_MaxColumnBlocks = 0;
	for (i = 0; i < p_Blocks.columns.size(); ++i)
	{
		unsigned uSize = p_Blocks.columns[i].size();

		if (p_MaxColumnBlocks < uSize)
		{
			p_MaxColumnBlocks = uSize;
		}
	}

	p_MaxRowBlocks = 0;
	for (i = 0; i < p_Blocks.rows.size(); ++i)
	{
		unsigned uSize = p_Blocks.rows[i].size();

		if (p_MaxRowBlocks < uSize)
		{
			p_MaxRowBlocks = uSize;
		}
	}
}

/** @brief AllocateGrid
  *
  * @todo: document this function
  */
void CrucipixelGrid::AllocateGrid()
{
	if (!p_Initialized)
	{
		return;
	}

	p_Grid = new cell *[p_Columns];
	for (unsigned i = 0; i < p_Columns; ++i)
	{
		p_Grid[i] = new cell[p_Rows];

		for (unsigned k = 0; k < p_Rows; ++k)
		{
			p_Grid[i][k] = Empty;
		}
	}
}

/** @brief FreeGrid
  *
  * @todo: document this function
  */
void CrucipixelGrid::FreeGrid()
{
	if (!p_Initialized)
	{
		return;
	}

	for (unsigned i = 0; i < p_Columns; ++i)
	{
		delete p_Grid[i];
	}
	delete p_Grid;
}
